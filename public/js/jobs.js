const API_BASE_JOBS = "/webkiemthu/api";

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

  const url = new URL(`${API_BASE_JOBS}/jobs.php`, window.location.origin);
  if (keyword) url.searchParams.set("keyword", keyword);
  if (location) url.searchParams.set("location", location);
  url.searchParams.set("page", page);

  // Thêm các tham số lọc vào URL
  if (fields) url.searchParams.set("field", fields);
  if (experiences) url.searchParams.set("experience", experiences);
  if (types) url.searchParams.set("type", types);

  // Gọi AI phân tích song song (không chặn việc tìm kiếm cơ bản)
  if (keyword.length > 2) {
    analyzeKeyword(keyword).then((analysis) => {
      if (analysis) {
        console.log("🔍 AI Phân tích từ khóa:", analysis);
        // Tại đây bạn có thể code thêm logic để tự động tick vào các checkbox bộ lọc dựa trên analysis.field hoặc analysis.experience
      }
    });
  }

  try {
    const res = await fetch(url);
    const data = await res.json();
    const jobs = data.jobs || [];

    const container = document.getElementById("job-list");
    if (!container) return;

    if (!Array.isArray(jobs) || jobs.length === 0) {
      container.innerHTML =
        '<div class="col-span-full text-center py-10 text-slate-500">Không tìm thấy công việc nào phù hợp.</div>';
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
          <button class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
            <span class="material-symbols-outlined">favorite</span>
          </button>
        </div>
        <div class="mb-4">
          <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
            <a href="chitietcongviec.php?id=${j.id}">${j.title}</a>
          </h3>
          <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
            ${j.company}
          </p>
        </div>
        <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">location_on</span>
            <span>${j.location}</span>
          </div>
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-base">attach_money</span>
            <span>${j.salary}</span>
          </div>
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
    renderJobPagination(data.pagination);
  } catch (err) {
    console.error("Lỗi tải danh sách việc làm:", err);
    const container = document.getElementById("job-list");
    if (container) {
      container.innerHTML = `<div class="col-span-full text-center text-red-500 py-10">
        <p class="font-bold">Lỗi tải danh sách việc làm.</p><p>${err.message}</p>
      </div>`;
    }
    renderJobPagination(null);
  }
}

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
      document.getElementById("job-salary").textContent = j.salary;

    // Với mô tả có thể chứa HTML
    if (document.getElementById("job-description"))
      document.getElementById("job-description").innerHTML = j.description;
  } catch (err) {
    console.error(err);
  }
}

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
      window.location.href = "timkiem.php";
    } else {
      alert(result.message || "Có lỗi xảy ra.");
    }
  } catch (err) {
    console.error(err);
    alert(err.message || "Lỗi kết nối đến máy chủ.");
  }
}

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

function renderJobPagination(pagination) {
  const container = document.getElementById("pagination-container-jobs");
  if (!container) return;

  if (!pagination || pagination.totalPages <= 1) {
    container.innerHTML = ""; // Không cần phân trang
    return;
  }

  const { page, totalPages, totalJobs } = pagination;
  const limit = 6; // Phải khớp với backend
  let paginationHTML = `
    <div class="flex flex-col sm:flex-row sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-2 sm:mb-0">
                Hiển thị <span class="font-medium">${(page - 1) * limit + 1}</span> đến
                <span class="font-medium">${Math.min(page * limit, totalJobs)}</span> trong số
                <span class="font-medium">${totalJobs}</span> công việc
            </p>
        </div>
        <div>
            <nav aria-label="Pagination" class="isolate inline-flex -space-x-px rounded-md shadow-sm">
    `;

  // Nút Previous
  paginationHTML += `
        <a href="#" onclick="event.preventDefault(); searchJobs(${page - 1})" 
           class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 ${page <= 1 ? "pointer-events-none opacity-50" : ""}">
            <span class="sr-only">Previous</span>
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </a>`;

  // Các nút số trang
  for (let i = 1; i <= totalPages; i++) {
    if (i === page) {
      paginationHTML += `<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">${i}</a>`;
    } else {
      paginationHTML += `<a href="#" onclick="event.preventDefault(); searchJobs(${i})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20">${i}</a>`;
    }
  }

  // Nút Next
  paginationHTML += `
        <a href="#" onclick="event.preventDefault(); searchJobs(${page + 1})" 
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

// Tự động tải danh sách khi ở trang tìm kiếm
if (document.getElementById("job-list")) {
  // Gắn sự kiện cho form tìm kiếm chính
  const searchForm = document.getElementById("search-form");
  if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
      e.preventDefault();
      searchJobs(1); // Tìm kiếm lại từ trang 1
    });
  }

  // Gắn sự kiện cho các checkbox bộ lọc
  const filterCheckboxes = document.querySelectorAll(
    '#advanced-filters input[type="checkbox"]',
  );
  filterCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      searchJobs(1); // Tìm kiếm lại từ trang 1 khi bộ lọc thay đổi
    });
  });

  searchJobs();
}
