<!doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Quản lý tin đăng - DDS</title>
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
      <div id="job-list-section" class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Quản lý tin tuyển dụng</h1>
          <p class="text-secondary-text mt-1">Xem và quản lý các vị trí bạn đang tuyển dụng.</p>
        </div>
        <a href="dangtin.php"
          class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-sm shadow-blue-500/30">
          <span class="material-symbols-outlined text-[20px] mr-2">add</span>
          Đăng tin mới
        </a>
      </div>

      <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-border-color dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr
                class="bg-gray-50 dark:bg-gray-700/50 border-b border-border-color dark:border-gray-700 text-xs uppercase text-secondary-text font-semibold">
                <th class="p-4">Công việc</th>
                <th class="p-4">Ngày đăng</th>
                <th class="p-4">Trạng thái</th>
                <th class="p-4">Ứng viên</th>
                <th class="p-4 text-right">Hành động</th>
              </tr>
            </thead>
            <tbody id="my-jobs-list" class="divide-y divide-border-color dark:divide-gray-700 text-sm">
              <!-- Dữ liệu sẽ được load bởi jobs.js -->
              <tr>
                <td colspan="5" class="p-8 text-center text-secondary-text">
                  <div class="flex flex-col items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-2"></div>
                    Đang tải dữ liệu...
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div id="pagination-container-my-jobs" class="p-4 border-t border-border-color dark:border-gray-700"></div>
      </div>
    </main>

    <script src="./js/utils.js"></script>
    <script src="./js/auth.js"></script>
    <script src="./js/jobs.js"></script>
  </body>

</html>
