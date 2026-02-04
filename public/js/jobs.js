const API_BASE_JOBS = "/webkiemthu/api";

// Đảm bảo hàm formatSalary tồn tại (phòng trường hợp utils.js bị cache cũ hoặc chưa tải)
if (typeof formatSalary === "undefined") {
  window.formatSalary = function (salary) {
    if (!salary) return "Thỏa thuận";
    if (!isNaN(salary) && !isNaN(parseFloat(salary))) {
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      })
        .format(salary)
        .replace("₫", "VNĐ");
    }
    return salary;
  };
}

// Biến toàn cục để lưu trạng thái tìm kiếm hiện tại
let currentSearchParams = {
  keyword: "",
  location: "",
  field: [],
  experience: [],
  type: [],
  salary_range: [],
};

// Biến toàn cục để lưu trang hiện tại của trang quản lý tin đăng
let myJobsCurrentPage = 1;

/**
 * Hàm tìm kiếm công việc với phân trang
 * @param {number} page - Trang hiện tại
 */
async function searchJobs(page = 1) {
  const keyword = document.getElementById("keyword")?.value || "";
  const location = document.getElementById("location")?.value || "";

  // Lấy giá trị từ các bộ lọc nâng cao
  const fields = Array.from(
    document.querySelectorAll('input[name="field"]:checked'),
  )
    .map((el) => el.value)
    .join(",");
  const experiences = Array.from(
    document.querySelectorAll('input[name="experience"]:checked'),
  )
    .map((el) => el.value)
    .join(",");
  const types = Array.from(
    document.querySelectorAll('input[name="type"]:checked'),
  )
    .map((el) => el.value)
    .join(",");
  const salaryRanges = Array.from(
    document.querySelectorAll('input[name="salary_range"]:checked'),
  )
    .map((el) => el.value)
    .join(",");

  // Lưu trạng thái tìm kiếm
  currentSearchParams = {
    keyword,
    location,
    field: fields,
    experience: experiences,
    type: types,
    salary_range: salaryRanges,
  };

  const url = new URL(`${API_BASE_JOBS}/jobs.php`, window.location.origin);
  if (keyword) url.searchParams.set("keyword", keyword);
  if (location) url.searchParams.set("location", location);
  url.searchParams.set("page", page);

  // Thêm các tham số lọc vào URL
  if (fields) url.searchParams.set("field", fields);
  if (experiences) url.searchParams.set("experience", experiences);
  if (types) url.searchParams.set("type", types);
  if (salaryRanges) url.searchParams.set("salary_range", salaryRanges);

  // Hiển thị loading state
  const container = document.getElementById("job-list");
  if (container) {
    container.innerHTML = `
      <div class="col-span-full flex flex-col items-center justify-center py-16 text-slate-500">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
        <p class="font-medium">Đang tải danh sách việc làm...</p>
      </div>
    `;
  }

  // Gọi AI phân tích song song (không chặn việc tìm kiếm cơ bản)
  if (keyword.length > 2) {
    analyzeKeyword(keyword).then((analysis) => {
      if (analysis && analysis.field) {
        console.log("🔍 AI Phân tích từ khóa:", analysis);
        // Có thể tự động suggest bộ lọc dựa trên AI analysis
      }
    });
  }

  try {
    const res = await fetch(url);

    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }

    const data = await res.json();

    // Kiểm tra cấu trúc response
    if (!data.success) {
      throw new Error(data.message || "Lỗi không xác định từ server");
    }

    const jobs = data.jobs || [];
    const pagination = data.pagination || null;

    if (!container) return;

    if (!Array.isArray(jobs) || jobs.length === 0) {
      container.innerHTML = `
        <div class="col-span-full text-center py-16">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
            <span class="material-symbols-outlined text-3xl text-gray-400">search_off</span>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Không tìm thấy công việc</h3>
          <p class="text-slate-500 dark:text-slate-400">Thử điều chỉnh bộ lọc hoặc từ khóa tìm kiếm của bạn</p>
        </div>
      `;
      renderJobPagination(null);
      return;
    }

    container.innerHTML = jobs
      .map(
        (j) => `
      <div class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
        <div class="flex justify-between items-start mb-4">
          <div class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
             <div class="w-8 h-8 rounded bg-primary flex items-center justify-center text-white font-bold">
                  ${j.company ? j.company.charAt(0).toUpperCase() : "C"}
             </div>
          </div>
          <button class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors" onclick="toggleSaveJob(${j.id})">
            <span class="material-symbols-outlined">favorite</span>
          </button>
        </div>
        <div class="mb-4">
          <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
            <a href="chitietcongviec.php?id=${j.id}">${escapeHtml(j.title)}</a>
          </h3>
          <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
            ${escapeHtml(j.company)}
          </p>
        </div>
        <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">location_on</span>
            <span>${escapeHtml(j.location)}</span>
          </div>
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">payments</span>
            <span>${escapeHtml(formatSalary(j.salary))}</span>
          </div>
          ${
            j.experience
              ? `
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">work_history</span>
            <span>${escapeHtml(j.experience)}</span>
          </div>
          `
              : ""
          }
        </div>
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
           <a href="chitietcongviec.php?id=${j.id}" class="flex items-center justify-center w-full bg-primary hover:bg-blue-600 text-white font-bold py-2 rounded-lg transition-colors">
             Xem chi tiết
           </a>
        </div>
      </div>
    `,
      )
      .join("");

    // Render phân trang
    renderJobPagination(pagination);

    // Scroll to top of results
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  } catch (err) {
    console.error("Lỗi tải danh sách việc làm:", err);
    if (container) {
      container.innerHTML = `
        <div class="col-span-full text-center py-16">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/20 mb-4">
            <span class="material-symbols-outlined text-3xl text-red-600 dark:text-red-400">error</span>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Lỗi tải dữ liệu</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-4">${escapeHtml(err.message)}</p>
          <button onclick="searchJobs(1)" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
            Thử lại
          </button>
        </div>
      `;
    }
    renderJobPagination(null);
  }
}

/**
 * Hàm toggle save job (placeholder)
 */
function toggleSaveJob(jobId) {
  console.log("Toggle save job:", jobId);
  // TODO: Implement save job functionality
}

/**
 * Hàm tải chi tiết công việc
 */
async function loadJobDetail(id) {
  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${id}`);
    if (!res.ok) throw new Error("Không thể tải chi tiết công việc");
    const j = await res.json();

    if (!j) {
      alert("Công việc không tồn tại hoặc đã bị xóa.");
      return;
    }

    // Cập nhật dữ liệu vào giao diện nếu tìm thấy phần tử
    if (document.getElementById("job-title"))
      document.getElementById("job-title").textContent = j.title;
    if (document.getElementById("job-company"))
      document.getElementById("job-company").textContent = j.company;
    if (document.getElementById("job-location"))
      document.getElementById("job-location").textContent = j.location;
    if (document.getElementById("job-salary"))
      document.getElementById("job-salary").textContent = formatSalary(
        j.salary,
      );

    // Với mô tả có thể chứa HTML
    if (document.getElementById("job-description"))
      document.getElementById("job-description").innerHTML = j.description;
  } catch (err) {
    console.error(err);
    alert("Không thể tải thông tin công việc: " + err.message);
  }
}

/**
 * Hàm tạo công việc mới
 */
async function createJob(e) {
  e.preventDefault();
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Vui lòng đăng nhập lại.");
    window.location.href = "dangnhap.php";
    return;
  }

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());

  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });

    const text = await res.text();
    let result;
    try {
      result = JSON.parse(text);
    } catch (err) {
      console.error("Lỗi phản hồi server:", text);
      throw new Error(
        "Lỗi hệ thống: Server trả về dữ liệu không hợp lệ (xem console).",
      );
    }

    if (res.ok) {
      alert("Đăng tin thành công!");
      window.location.href = "quanlytindang.php";
    } else {
      alert(result.message || "Có lỗi xảy ra.");
    }
  } catch (err) {
    console.error(err);
    alert(err.message || "Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Hàm cập nhật công việc
 */
async function updateJob(e, jobId) {
  e.preventDefault();
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Vui lòng đăng nhập lại.");
    window.location.href = "dangnhap.php";
    return;
  }

  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData.entries());

  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${jobId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(data),
    });

    const text = await res.text();
    let result;
    try {
      result = JSON.parse(text);
    } catch (err) {
      console.error("Lỗi phản hồi server:", text);
      throw new Error(
        "Lỗi hệ thống: Server trả về dữ liệu không hợp lệ (xem console).",
      );
    }

    if (res.ok) {
      alert("Cập nhật tin thành công!");
      window.location.href = "quanlytindang.php";
    } else {
      alert(result.message || "Có lỗi xảy ra.");
    }
  } catch (err) {
    console.error(err);
    alert(err.message || "Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Hàm tải danh sách công việc đã đăng của nhà tuyển dụng
 * @param {number} page - Trang hiện tại
 */
async function loadMyJobs(page = 1) {
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Bạn cần đăng nhập để xem trang này.");
    window.location.href = "dangnhap.php";
    return;
  }

  // Lưu lại trang hiện tại để có thể reload
  myJobsCurrentPage = page;

  const container = document.getElementById("my-jobs-list");
  if (!container) return;

  // Hiển thị trạng thái tải
  container.innerHTML = `
    <tr>
      <td colspan="5" class="text-center py-16">
        <div class="flex flex-col items-center justify-center text-slate-500">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
          <p class="font-medium">Đang tải danh sách tin đã đăng...</p>
        </div>
      </td>
    </tr>
  `;

  const url = new URL(`${API_BASE_JOBS}/jobs.php`, window.location.origin);
  url.searchParams.set("view", "employer");
  url.searchParams.set("page", page);

  try {
    const res = await fetch(url, {
      headers: { Authorization: `Bearer ${token}` },
    });

    if (!res.ok) {
      const errorData = await res
        .json()
        .catch(() => ({ message: "Lỗi không xác định từ máy chủ." }));
      if (res.status === 401) {
        alert("Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.");
        window.location.href = "dangnhap.php";
      }
      throw new Error(errorData.message || `Lỗi máy chủ: ${res.status}`);
    }

    const data = await res.json();
    if (!data.success) {
      throw new Error(data.message || "Lỗi không xác định từ server");
    }

    const jobs = data.jobs || [];
    const pagination = data.pagination || null;

    if (jobs.length === 0) {
      container.innerHTML = `
        <tr>
          <td colspan="5" class="text-center py-16">
            <div class="flex flex-col items-center justify-center text-slate-500">
              <span class="material-symbols-outlined text-5xl mb-4">post_add</span>
              <p class="text-lg font-medium">Bạn chưa đăng tin tuyển dụng nào.</p>
              <a href="quanlytindang.php#post-job-section" class="mt-4 px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
                Đăng tin ngay
              </a>
            </div>
          </td>
        </tr>
      `;
      renderMyJobsPagination(null);
      return;
    }

    // Giả sử container là một <tbody>
    container.innerHTML = jobs
      .map(
        (job) => `
      <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
        <td class="p-4">
          <p class="font-bold text-slate-800 dark:text-slate-200">${escapeHtml(job.title)}</p>
          <p class="text-sm text-slate-500">${escapeHtml(job.location)}</p>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">${new Date(job.created_at).toLocaleDateString("vi-VN")}</td>
        <td class="p-4">
          <span class="px-2 py-1 text-xs font-semibold rounded-full ${job.status === "open" ? "bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300" : "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"}">
            ${job.status === "open" ? "Đang hiển thị" : "Đã đóng"}
          </span>
        </td>
        <td class="p-4 text-sm text-slate-600 dark:text-slate-400">0</td> <!-- Placeholder for applicant count -->
        <td class="p-4 text-right">
          <div class="flex items-center justify-end gap-4">
            <a href="xemungvien.php?job_id=${job.id}" class="text-sm font-medium text-primary hover:underline">Xem ứng viên</a>
            <a href="suatin.php?id=${job.id}" class="text-sm font-medium text-slate-500 hover:text-primary">Sửa</a>
            <button onclick="toggleJobStatus(${job.id}, '${job.status}')" class="text-sm font-medium ${job.status === "open" ? "text-red-500 hover:text-red-700" : "text-green-500 hover:text-green-700"} transition-colors">
              ${job.status === "open" ? "Đóng tin" : "Mở lại"}
            </button>
            <button onclick="deleteJob(${job.id})" class="text-sm font-medium text-gray-400 hover:text-red-600 transition-colors" title="Xóa tin">
              Xóa
            </button>
          </div>
        </td>
      </tr>
    `,
      )
      .join("");

    renderMyJobsPagination(pagination);
  } catch (e) {
    console.error("Lỗi tải tin đã đăng:", e);
    container.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-16">
          <div class="flex flex-col items-center justify-center text-red-500">
            <span class="material-symbols-outlined text-5xl mb-4">error</span>
            <h3 class="text-lg font-semibold mb-2">Lỗi tải dữ liệu</h3>
            <p class="text-slate-500 mb-4">${escapeHtml(e.message)}</p>
            <button onclick="loadMyJobs(1)" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors">
              Thử lại
            </button>
          </div>
        </td>
      </tr>
    `;
    renderMyJobsPagination(null);
  }
}

/**
 * Hàm Đóng/Mở tin tuyển dụng
 * @param {number} jobId ID của công việc
 * @param {string} currentStatus Trạng thái hiện tại ('open' hoặc 'closed')
 */
async function toggleJobStatus(jobId, currentStatus) {
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Vui lòng đăng nhập lại để thực hiện thao tác này.");
    window.location.href = "dangnhap.php";
    return;
  }

  const newStatus = currentStatus === "open" ? "closed" : "open";
  const actionText = newStatus === "closed" ? "đóng" : "mở lại";

  if (!confirm(`Bạn có chắc muốn ${actionText} tin tuyển dụng này không?`)) {
    return;
  }

  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${jobId}`, {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ status: newStatus }),
    });

    const result = await res.json();

    if (res.ok) {
      alert(result.message || "Cập nhật trạng thái thành công!");
      loadMyJobs(myJobsCurrentPage); // Tải lại danh sách ở trang hiện tại
    } else {
      alert(result.message || "Có lỗi xảy ra, không thể cập nhật.");
    }
  } catch (err) {
    console.error("Lỗi khi cập nhật trạng thái công việc:", err);
    alert("Lỗi kết nối đến máy chủ. Vui lòng thử lại.");
  }
}

/**
 * Hàm xóa mềm tin tuyển dụng
 * @param {number} jobId ID của công việc
 */
async function deleteJob(jobId) {
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Vui lòng đăng nhập lại.");
    window.location.href = "dangnhap.php";
    return;
  }

  if (
    !confirm(
      "Bạn có chắc chắn muốn xóa tin tuyển dụng này? Hành động này sẽ ẩn tin khỏi hệ thống.",
    )
  ) {
    return;
  }

  try {
    const res = await fetch(`${API_BASE_JOBS}/jobs.php?id=${jobId}`, {
      method: "DELETE",
      headers: { Authorization: `Bearer ${token}` },
    });
    const data = await res.json();
    alert(data.message || "Đã xóa thành công!");
    loadMyJobs(myJobsCurrentPage);
  } catch (err) {
    console.error(err);
    alert("Lỗi kết nối đến máy chủ.");
  }
}

/**
 * Hàm render phân trang cho danh sách công việc của nhà tuyển dụng
 */
function renderMyJobsPagination(pagination) {
  const container = document.getElementById("pagination-container-my-jobs");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = "";
    return;
  }

  const { page, totalPages, totalJobs, limit, hasNextPage, hasPrevPage } =
    pagination;
  const startItem = (page - 1) * limit + 1;
  const endItem = Math.min(page * limit, totalJobs);

  let paginationHTML = `
    <div class="flex flex-col sm:flex-row sm:flex-1 sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Hiển thị <span class="font-medium">${startItem}</span> đến
                <span class="font-medium">${endItem}</span> trong số
                <span class="font-medium">${totalJobs}</span> tin đã đăng
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
  `;

  // Previous button
  paginationHTML += `
    <a href="#" 
       onclick="event.preventDefault(); ${hasPrevPage ? `loadMyJobs(${page - 1})` : "return false;"}" 
       class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasPrevPage ? "pointer-events-none opacity-50" : ""}">
        <span class="sr-only">Previous</span>
        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
    </a>`;

  const pageNumbers = getPageNumbers(page, totalPages);
  pageNumbers.forEach((pageNum) => {
    if (pageNum === "...") {
      paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">...</span>`;
    } else if (pageNum === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${pageNum}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); loadMyJobs(${pageNum})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 transition-colors">${pageNum}</a>`;
    }
  });

  // Next button
  paginationHTML += `
    <a href="#" 
       onclick="event.preventDefault(); ${hasNextPage ? `loadMyJobs(${page + 1})` : "return false;"}" 
       class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasNextPage ? "pointer-events-none opacity-50" : ""}">
        <span class="sr-only">Next</span>
        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
    </a>`;

  paginationHTML += `</nav></div></div>`;
  container.innerHTML = paginationHTML;
}

/**
 * Hàm phân tích từ khóa bằng AI
 */
async function analyzeKeyword(keyword) {
  try {
    const res = await fetch(`${API_BASE_JOBS}/analyze.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ keyword }),
    });
    const data = await res.json();
    return data;
  } catch (err) {
    console.error("Lỗi phân tích từ khóa:", err);
    return null;
  }
}

/**
 * Hàm render phân trang cho danh sách công việc
 */
function renderJobPagination(pagination) {
  const container = document.getElementById("pagination-container-jobs");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = ""; // Không cần phân trang
    return;
  }

  const { page, totalPages, totalJobs, limit, hasNextPage, hasPrevPage } =
    pagination;

  // Tính toán số hiển thị
  const startItem = (page - 1) * limit + 1;
  const endItem = Math.min(page * limit, totalJobs);

  let paginationHTML = `
    <div class="flex flex-col sm:flex-row sm:flex-1 sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Hiển thị <span class="font-medium">${startItem}</span> đến
                <span class="font-medium">${endItem}</span> trong số
                <span class="font-medium">${totalJobs}</span> công việc
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
  `;

  // Nút Previous
  paginationHTML += `
        <a href="#" 
           onclick="event.preventDefault(); ${hasPrevPage ? `searchJobs(${page - 1})` : "return false;"}" 
           class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasPrevPage ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Previous</span>
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>`;

  // Tạo danh sách các trang hiển thị
  const pageNumbers = getPageNumbers(page, totalPages);

  pageNumbers.forEach((pageNum) => {
    if (pageNum === "...") {
      paginationHTML += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">...</span>`;
    } else if (pageNum === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${pageNum}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); searchJobs(${pageNum})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 transition-colors">${pageNum}</a>`;
    }
  });

  // Nút Next
  paginationHTML += `
        <a href="#" 
           onclick="event.preventDefault(); ${hasNextPage ? `searchJobs(${page + 1})` : "return false;"}" 
           class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0 transition-colors ${!hasNextPage ? "pointer-events-none opacity-50" : ""}">
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

/**
 * Khởi tạo khi trang được load
 */
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("job-list")) {
    // Gắn sự kiện cho form tìm kiếm chính
    const searchForm = document.getElementById("search-form");
    if (searchForm) {
      searchForm.addEventListener("submit", (e) => {
        e.preventDefault();
        searchJobs(1); // Tìm kiếm lại từ trang 1
      });
    }

    // Gắn sự kiện cho nút tìm kiếm (nếu có)
    const searchButton = document.querySelector(
      'button[onclick="searchJobs()"]',
    );
    if (searchButton) {
      searchButton.onclick = (e) => {
        e.preventDefault();
        searchJobs(1);
      };
    }

    // Gắn sự kiện cho các checkbox bộ lọc
    const filterCheckboxes = document.querySelectorAll(
      'input[type="checkbox"][name="field"], input[type="checkbox"][name="experience"], input[type="checkbox"][name="type"], input[type="checkbox"][name="salary_range"]',
    );
    filterCheckboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        searchJobs(1); // Tìm kiếm lại từ trang 1 khi bộ lọc thay đổi
      });
    });

    // Tải danh sách ban đầu
    searchJobs(1);
  }

  // Khởi tạo cho trang quản lý tin đăng của nhà tuyển dụng
  if (document.getElementById("my-jobs-list")) {
    loadMyJobs(1);
  }
});
