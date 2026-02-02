const API_BASE_APP = "/webkiemthu/api";

async function applyJob(jobId, cvText) {
  if (!jobId) {
    alert("Không tìm thấy thông tin công việc để ứng tuyển!");
    return;
  }

  const token = localStorage.getItem("token");
  if (!token) {
    alert("Bạn cần đăng nhập để ứng tuyển!");
    window.location.href = "dangnhap.php";
    return;
  }

  if (!cvText || cvText.trim() === "") {
    alert("Vui lòng nhập giới thiệu bản thân hoặc link CV!");
    document.getElementById("cvText")?.focus();
    return;
  }

  try {
    const res = await fetch(`${API_BASE_APP}/applications.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ job_id: jobId, cv_text: cvText }),
    });

    const data = await res.json();

    if (res.ok) {
      window.location.href = "utthanhcong.php";
    } else {
      alert(data.message || "Có lỗi xảy ra khi ứng tuyển.");
    }
  } catch (error) {
    console.error(error);
    alert("Lỗi kết nối đến máy chủ.");
  }
}

// --- Helper Functions for rendering application cards ---

/**
 * Returns an HTML string for a status badge based on the application status.
 * @param {string} status - The status of the application.
 * @returns {string} HTML string for the badge.
 */
function getStatusBadge(status) {
  const s = status ? status.toLowerCase() : "submitted";
  switch (s) {
    case "interview":
    case "mời phỏng vấn":
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                  <span class="size-1.5 rounded-full bg-green-600 animate-pulse"></span>
                  Mời phỏng vấn
              </span>`;
    case "reviewed":
    case "ntd đã xem":
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                  <span class="material-symbols-outlined text-[16px]">visibility</span>
                  NTD đã xem
              </span>`;
    case "rejected":
    case "bị từ chối":
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200">
                  <span class="material-symbols-outlined text-[16px]">cancel</span>
                  Bị từ chối
              </span>`;
    case "submitted":
    case "đã nộp":
    default:
      return `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                  <span class="material-symbols-outlined text-[16px]">send</span>
                  Đã nộp
              </span>`;
  }
}

/**
 * Returns an HTML string for the CV link/text.
 * @param {string} cv_text - The CV content or link.
 * @returns {string} HTML string for the CV display.
 */
function getCvLink(cv_text) {
  if (!cv_text) return "<span>Không có thông tin</span>";
  try {
    // Check if it's a valid URL
    new URL(cv_text);
    const filename = cv_text.split("/").pop();
    return `<a class="text-primary hover:underline font-medium inline-flex items-center gap-1" href="${cv_text}" target="_blank" rel="noopener noreferrer">
              ${filename || "Link CV"}
              <span class="material-symbols-outlined text-[14px]">open_in_new</span>
          </a>`;
  } catch (_) {
    // If not a URL, treat as text. Show filename if it looks like one.
    if (
      cv_text.toLowerCase().endsWith(".pdf") ||
      cv_text.toLowerCase().endsWith(".doc") ||
      cv_text.toLowerCase().endsWith(".docx")
    ) {
      return `<span class="font-medium text-gray-700 dark:text-gray-300">${cv_text}</span>`;
    }
    // Otherwise, it's an introduction text
    return `<span class="font-medium text-gray-700 dark:text-gray-300">${cv_text.substring(0, 30)}...</span>`;
  }
}

/**
 * Formats a date string into a more readable format.
 * @param {string} dateString - The date string to format.
 * @returns {string} Formatted date string.
 */
function formatDate(dateString) {
  if (!dateString) return "Không rõ";
  const options = { day: "numeric", month: "long", year: "numeric" };
  return new Date(dateString).toLocaleDateString("vi-VN", options);
}

async function loadApplications(page = 1) {
  const token = localStorage.getItem("token");
  if (!token) return; // Chưa đăng nhập thì không tải

  const container = document.getElementById("application-list");
  if (!container) return;

  const url = new URL(
    `${API_BASE_APP}/applications.php`,
    window.location.origin,
  );
  url.searchParams.set("page", page);

  try {
    const res = await fetch(url, {
      headers: { Authorization: `Bearer ${token}` },
    });

    if (!res.ok) {
      const errorData = await res
        .json()
        .catch(() => ({ message: "Lỗi không xác định từ máy chủ." }));
      throw new Error(errorData.message || `Lỗi máy chủ: ${res.status}`);
    }

    const data = await res.json();
    const apps = data.applications || [];

    // Cập nhật các thẻ đếm (nếu có)
    if (data.pagination) {
      updateStatusCounts(data.pagination.totalApplications);
    }

    if (apps.length === 0) {
      container.innerHTML =
        '<div class="col-span-full flex flex-col items-center justify-center py-16 text-secondary-text"><span class="material-symbols-outlined text-5xl mb-4">work_history</span><p class="text-lg font-medium">Bạn chưa ứng tuyển công việc nào.</p><p class="text-sm">Hãy bắt đầu tìm kiếm và ứng tuyển ngay hôm nay!</p></div>';
      renderApplicationPagination(null);
      return;
    }

    container.innerHTML = apps
      .map((app) => {
        const isJobDeleted = !app.title;
        const title = app.title || "[Công việc đã bị xóa]";
        const company = app.company || "Không có thông tin";
        const companyInitial = app.company
          ? app.company.charAt(0).toUpperCase()
          : "?";
        const detailLink = isJobDeleted
          ? "#"
          : `chitietcongviec.php?id=${app.job_id}`;
        const linkClass = isJobDeleted ? "pointer-events-none opacity-50" : "";

        return `
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 p-5 flex flex-col hover:shadow-md transition-shadow group relative ${
            isJobDeleted ? "opacity-70" : ""
          }">
              <div class="flex items-start justify-between mb-4">
                  <div class="flex gap-4 items-center min-w-0">
                      <div class="size-14 rounded-lg bg-white border border-border-color flex items-center justify-center flex-shrink-0 p-1">
                           <div class="w-11 h-11 rounded bg-primary/10 flex items-center justify-center text-primary font-bold text-xl">
                              ${companyInitial}
                           </div>
                      </div>
                      <div class="overflow-hidden">
                          <a class="font-bold text-lg text-gray-900 dark:text-white leading-tight ${
                            isJobDeleted
                              ? ""
                              : "hover:text-primary transition-colors"
                          } block mb-1 truncate" href="${detailLink}" title="${title}">
                              ${title}
                          </a>
                          <p class="text-sm text-secondary-text font-medium truncate">
                              ${company}
                          </p>
                      </div>
                  </div>
              </div>
              <div class="space-y-3 mb-6 flex-1">
                  <div class="flex items-center gap-3 text-sm text-secondary-text">
                      <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                      <span>Ngày ứng tuyển:
                          <span class="font-medium text-gray-700 dark:text-gray-300">
                              ${formatDate(app.created_at)}
                          </span>
                      </span>
                  </div>
                  <div class="flex items-center gap-3 text-sm text-secondary-text">
                      <span class="material-symbols-outlined text-[18px]">description</span>
                      <span class="flex items-center gap-2">
                          CV đã nộp: ${getCvLink(app.cv_text)}
                      </span>
                  </div>
              </div>
              <div class="pt-4 border-t border-border-color dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                  ${getStatusBadge(app.status)}
                  <div class="flex items-center gap-3">
                      <button class="text-sm font-medium text-gray-500 hover:text-red-600 transition-colors px-2 py-1">Rút hồ sơ</button>
                      <a href="${detailLink}" class="text-sm font-medium text-white bg-primary hover:bg-blue-700 px-4 py-2 rounded-lg transition-colors shadow-sm shadow-blue-200 dark:shadow-none ${linkClass}">
                          Xem chi tiết
                      </a>
                  </div>
              </div>
          </div>
          `;
      })
      .join("");
    renderApplicationPagination(data.pagination);
  } catch (e) {
    console.error("Lỗi tải danh sách ứng tuyển:", e);
    if (container) {
      container.innerHTML = `<div class="col-span-full text-center text-red-500 py-16">
            <span class="material-symbols-outlined text-5xl mb-4">error</span>
            <p class="font-bold">Đã có lỗi xảy ra khi tải dữ liệu.</p>
            <p class="text-sm">${e.message}</p>
        </div>`;
    }
    renderApplicationPagination(null);
  }
}

function updateStatusCounts(total) {
  // This function updates the "Đã nộp" card, which represents the total applications.
  const submittedCountEl = document.getElementById("count-submitted");
  if (submittedCountEl) {
    submittedCountEl.textContent = total;
  }
  // To update other statuses, the API would need to be modified to return those counts.
}

function renderApplicationPagination(pagination) {
  const container = document.getElementById("pagination-container-apps");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = ""; // Không cần phân trang
    return;
  }

  const { page, totalPages, totalApplications } = pagination;
  let paginationHTML = `
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-secondary-text">
                Hiển thị <span class="font-medium">${(page - 1) * 6 + 1}</span> đến
                <span class="font-medium">${Math.min(page * 6, totalApplications)}</span> trong số
                <span class="font-medium">${totalApplications}</span> kết quả
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
    `;

  // Nút Previous
  paginationHTML += `
        <a href="#" onclick="event.preventDefault(); loadApplications(${page - 1})" 
           class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 ${page <= 1 ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Previous</span>
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>`;

  // Các nút số trang
  for (let i = 1; i <= totalPages; i++) {
    if (i === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${i}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); loadApplications(${i})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20">${i}</a>`;
    }
  }

  // Nút Next
  paginationHTML += `
        <a href="#" onclick="event.preventDefault(); loadApplications(${page + 1})" 
           class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 ${page >= totalPages ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Next</span>
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </a>`;

  paginationHTML += `
            </nav>
        </div>
    </div>
    `;

  container.innerHTML = paginationHTML;
}
