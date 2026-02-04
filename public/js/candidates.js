const API_BASE_CANDIDATES = "/webkiemthu/api";

document.addEventListener("DOMContentLoaded", () => {
  // Lấy job_id từ URL
  const urlParams = new URLSearchParams(window.location.search);
  const jobId = urlParams.get("job_id");

  if (!jobId) {
    alert("Không tìm thấy thông tin công việc.");
    window.location.href = "quanlytindang.php";
    return;
  }

  loadCandidates(jobId);
});

/**
 * Tải danh sách ứng viên
 */
async function loadCandidates(jobId, options = {}) {
  const { allowFallback = true } = options;
  const token = localStorage.getItem("token");
  if (!token) {
    alert("Vui lòng đăng nhập.");
    window.location.href = "dangnhap.php";
    return;
  }

  const container = document.getElementById("candidates-list");
  const titleEl = document.getElementById("job-title-display");
  const countEl = document.getElementById("candidate-count");

  try {
    const res = await fetch(
      `${API_BASE_CANDIDATES}/candidates.php?job_id=${jobId}`,
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );

    let data = {};
    try {
      data = await res.json();
    } catch (_) {
      data = {};
    }

    if (!res.ok) {
      if (allowFallback && (res.status === 403 || res.status === 404)) {
        const fallbackJobId = await getFirstEmployerJobId(token);
        if (fallbackJobId && String(fallbackJobId) !== String(jobId)) {
          history.replaceState(
            null,
            "",
            `xemungvien.php?job_id=${fallbackJobId}`,
          );
          return loadCandidates(fallbackJobId, { allowFallback: false });
        }
      }
      throw new Error(data.message || "Lỗi tải dữ liệu.");
    }

    // Cập nhật tiêu đề công việc
    if (titleEl) titleEl.textContent = data.job_title || "Không rõ";

    const candidates = data.candidates || [];
    if (countEl) countEl.textContent = candidates.length;

    if (candidates.length === 0) {
      container.innerHTML = `
                <tr>
                    <td colspan="5" class="p-8 text-center text-secondary-text">
                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">group_off</span>
                        <p>Chưa có ứng viên nào nộp hồ sơ cho công việc này.</p>
                    </td>
                </tr>
            `;
      return;
    }

    container.innerHTML = candidates
      .map((c) => {
        // Xử lý hiển thị CV (Link hoặc Text)
        const cvText = c.cv_text || "";
        let cvDisplay = "";
        try {
          if (!cvText) {
            throw new Error("empty");
          }
          new URL(cvText); // Kiểm tra nếu là URL
          cvDisplay = `<a href="${escapeHtml(cvText)}" target="_blank" class="text-primary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">description</span> Xem CV
                             </a>`;
        } catch (e) {
          if (!cvText) {
            cvDisplay = `<span class="text-gray-500 italic">Chưa có CV</span>`;
          } else {
            // Nếu là text thường
            cvDisplay = `<span class="text-gray-600 italic" title="${escapeHtml(cvText)}">"${escapeHtml(cvText.substring(0, 50))}${cvText.length > 50 ? "..." : ""}"</span>`;
          }
        }

        return `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="p-4">
                        <div class="font-medium text-gray-900 dark:text-white">${escapeHtml(c.candidate_name)}</div>
                    </td>
                    <td class="p-4 text-secondary-text">
                        <div class="flex items-center gap-1 text-sm">
                            <span class="material-symbols-outlined text-[16px]">mail</span>
                            ${escapeHtml(c.candidate_email)}
                        </div>
                    </td>
                    <td class="p-4 text-sm">
                        ${cvDisplay}
                    </td>
                    <td class="p-4 text-sm text-secondary-text">
                        ${new Date(c.created_at).toLocaleDateString("vi-VN")}
                    </td>
                    <td class="p-4">
                        <select onchange="updateCandidateStatus(${c.id}, this.value)" 
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-1 pl-2 pr-8 bg-white dark:bg-gray-700 dark:border-gray-600 ${getStatusColorClass(c.status)}">
                            <option value="submitted" ${c.status === "submitted" ? "selected" : ""}>Đã nộp</option>
                            <option value="reviewed" ${c.status === "reviewed" ? "selected" : ""}>Đã xem</option>
                            <option value="interview" ${c.status === "interview" ? "selected" : ""}>Mời phỏng vấn</option>
                            <option value="rejected" ${c.status === "rejected" ? "selected" : ""}>Từ chối</option>
                        </select>
                    </td>
                </tr>
            `;
      })
      .join("");
  } catch (err) {
    console.error(err);
    container.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-red-500">Lỗi: ${err.message}</td></tr>`;
  }
}

/**
 * Cập nhật trạng thái ứng viên
 */
async function updateCandidateStatus(appId, newStatus) {
  const token = localStorage.getItem("token");
  try {
    const res = await fetch(
      `${API_BASE_CANDIDATES}/candidates.php?id=${appId}`,
      {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ status: newStatus }),
      },
    );

    const data = await res.json();
    if (res.ok) {
      // Có thể hiển thị toast notification ở đây
      console.log("Cập nhật thành công");
    } else {
      alert(data.message || "Lỗi cập nhật trạng thái");
    }
  } catch (err) {
    console.error(err);
    alert("Lỗi kết nối server");
  }
}

function getStatusColorClass(status) {
  switch (status) {
    case "interview":
      return "text-green-600 font-medium";
    case "rejected":
      return "text-red-600";
    case "reviewed":
      return "text-blue-600";
    default:
      return "text-gray-600";
  }
}

async function getFirstEmployerJobId(token) {
  try {
    const res = await fetch(
      `${API_BASE_CANDIDATES}/jobs.php?view=employer&page=1`,
      {
        headers: { Authorization: `Bearer ${token}` },
      },
    );
    if (!res.ok) return null;
    const data = await res.json();
    const jobs = data.jobs || [];
    return jobs.length > 0 ? jobs[0].id : null;
  } catch (err) {
    console.error(err);
    return null;
  }
}
