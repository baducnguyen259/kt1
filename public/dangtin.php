<!doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng tin tuyển dụng - DDS</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap"
      rel="stylesheet" />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
      rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#066bf9",
            "background-light": "#f5f7f8",
            "background-dark": "#0f1723",
            "secondary-text": "#476b9e",
            "border-color": "#e6ecf4",
          },
          fontFamily: {
            display: ["Inter", "sans-serif"],
          },
        },
      },
    };
    </script>
    <style>
    body {
      font-family: "Inter", sans-serif;
    }

    .material-symbols-outlined {
      font-variation-settings: "FILL"0, "wght"400, "GRAD"0, "opsz"24;
    }
    </style>
  </head>

  <body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display text-[#0d131c]">
    <!-- Header -->
    <header class="bg-white dark:bg-[#1a202c] border-b border-border-color dark:border-gray-800 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center gap-8">
            <a class="flex items-center gap-2 group" href="timkiem.php">
              <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
                <span class="material-symbols-outlined text-[20px]">work</span>
              </div>
              <h2
                class="text-xl font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-primary transition-colors">
                DDS</h2>
            </a>
          </div>
          <div id="auth-buttons" class="flex items-center gap-3">
            <!-- Auth buttons injected by auth.js -->
          </div>
        </div>
      </div>
    </header>

    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
      <div id="post-job-section"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 p-6 mb-8">
        <div class="mb-6 border-b border-border-color dark:border-gray-700 pb-4">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-white">Đăng tin tuyển dụng mới</h2>
              <p class="text-secondary-text mt-1">Nhập thông tin chi tiết về vị trí bạn đang tìm kiếm.</p>
            </div>
            <a href="quanlytindang.php"
              class="inline-flex items-center justify-center px-3 py-2 rounded-lg border border-border-color dark:border-gray-600 text-sm font-semibold text-secondary-text hover:text-primary hover:border-primary transition-colors">
              Xem danh sách tin đã đăng
            </a>
          </div>
        </div>

        <form id="postJobForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tiêu đề công việc
                <span class="text-red-500">*</span></label>
              <input type="text" name="title" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Senior Frontend Developer">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tên công ty <span
                  class="text-red-500">*</span></label>
              <input type="text" name="company" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Tech Corp">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Địa điểm <span
                  class="text-red-500">*</span></label>
              <input type="text" name="location" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: Hà Nội">
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mức lương <span
                  class="text-red-500">*</span></label>
              <input type="text" name="salary" required
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Ví dụ: 20 - 30 triệu hoặc Thỏa thuận">
            </div>

            <div class="col-span-full">
              <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Mô tả công
                việc</label>
              <textarea name="description" rows="6"
                class="w-full rounded-lg border-slate-300 dark:border-gray-600 bg-slate-50 dark:bg-[#0f1723] text-gray-900 dark:text-white focus:ring-primary focus:border-primary"
                placeholder="Mô tả chi tiết, yêu cầu, quyền lợi..."></textarea>
            </div>
          </div>

          <div class="pt-2 flex flex-wrap justify-end gap-3">
            <button type="reset"
              class="px-5 py-2.5 rounded-lg border border-border-color dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Làm
              mới</button>
            <button type="submit"
              class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">Đăng
              tin ngay</button>
          </div>
        </form>
      </div>
    </main>

    <script src="./js/utils.js"></script>
    <script src="./js/auth.js"></script>
    <script src="./js/jobs.js"></script>
    <script>
    const postJobForm = document.getElementById("postJobForm");
    if (postJobForm) {
      postJobForm.addEventListener("submit", createJob);
    }
    </script>
  </body>

</html>