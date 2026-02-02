<!doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Trang Web Tìm Việc Làm - Biến Thể 3</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"
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
            "card-light": "#ffffff",
            "card-dark": "#1e293b",
          },
          fontFamily: {
            display: ["Inter", "sans-serif"],
          },
          borderRadius: {
            DEFAULT: "0.25rem",
            lg: "0.5rem",
            xl: "0.75rem",
            "2xl": "1rem",
            full: "9999px",
          },
        },
      },
    };
    </script>
    <style>
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 20px;
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
      background-color: #475569;
    }
    </style>
  </head>

  <body
    class="bg-background-light dark:bg-background-dark font-display flex flex-col min-h-screen text-slate-900 dark:text-slate-100 transition-colors duration-200">
    <header
      class="sticky top-0 z-50 bg-white/95 dark:bg-[#111a25]/95 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <a href="timkiem.php" class="flex items-center gap-2">
            <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
              <span class="material-symbols-outlined text-[20px]">work</span>
            </div>
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">DDS</span>
          </a>
          <div class="hidden md:flex flex-1 items-center justify-center gap-8">
            <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="timkiem.php">Việc làm</a>
            <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="trangthai.php">Hồ sơ &amp; CV</a>
            <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="#">Công cụ</a>
            <a class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-primary dark:hover:text-primary transition-colors"
              href="#">Cẩm nang</a>
          </div>
          <div id="auth-buttons" class="flex items-center gap-3">
            <a class="hidden sm:flex h-9 px-4 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors mr-2"
              href="dangtuyen.php">Đăng tin</a>
            <a href="dangnhap.php"
              class="hidden sm:flex h-9 px-4 items-center justify-center rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
              Đăng nhập
            </a>
            <a href="dangky.php"
              class="h-9 px-4 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm shadow-blue-500/20">
              Đăng ký
            </a>
          </div>
        </div>
      </div>
    </header>
    <div
      class="relative bg-white dark:bg-[#111a25] border-b border-slate-200 dark:border-slate-800 pb-10 pt-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-3">
          Tìm việc làm mơ ước ngay hôm nay
        </h1>
        <p class="text-slate-500 dark:text-slate-400">
          Khám phá hàng ngàn cơ hội việc làm với đầy đủ thông tin bạn cần.
        </p>
      </div>
      <div class="max-w-5xl mx-auto">
        <div
          class="bg-white dark:bg-[#1e293b] p-3 rounded-2xl shadow-lg shadow-slate-200/50 dark:shadow-black/20 border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row gap-3">
          <div class="flex-1 relative group">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-slate-400">search</span>
            </div>
            <input id="keyword"
              class="w-full h-12 pl-10 pr-4 bg-slate-50 dark:bg-[#0f1723] border-transparent focus:border-primary focus:ring-0 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 text-sm transition-colors"
              placeholder="Vị trí ứng tuyển, từ khóa..." type="text" />
          </div>
          <div class="flex-1 relative group">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-slate-400">category</span>
            </div>
            <select
              class="w-full h-12 pl-10 pr-8 bg-slate-50 dark:bg-[#0f1723] border-transparent focus:border-primary focus:ring-0 rounded-xl text-slate-900 dark:text-white text-sm appearance-none cursor-pointer transition-colors">
              <option disabled="" selected="" value="">Chọn ngành nghề</option>
              <option value="tech">Công nghệ thông tin</option>
              <option value="finance">Tài chính / Kế toán</option>
              <option value="marketing">Marketing / Truyền thông</option>
              <option value="design">Thiết kế</option>
            </select>
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-slate-400 text-sm">expand_more</span>
            </div>
          </div>
          <div class="flex-1 relative group">
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
              <span class="material-symbols-outlined text-slate-400">location_on</span>
            </div>
            <input id="location"
              class="w-full h-12 pl-10 pr-4 bg-slate-50 dark:bg-[#0f1723] border-transparent focus:border-primary focus:ring-0 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 text-sm transition-colors"
              placeholder="Tỉnh/Thành phố..." type="text" />
          </div>
          <button onclick="searchJobs()"
            class="h-12 px-8 bg-primary hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
            <span>Tìm kiếm</span>
          </button>
        </div>
      </div>
    </div>
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex flex-col lg:flex-row gap-8">
      <aside class="w-full lg:w-72 flex-shrink-0 space-y-8">
        <div class="flex items-center justify-between lg:hidden mb-4">
          <button class="flex items-center gap-2 text-sm font-semibold text-primary">
            <span class="material-symbols-outlined">filter_list</span>
            Hiện bộ lọc
          </button>
          <span class="text-sm text-slate-500">142 việc làm</span>
        </div>
        <div class="hidden lg:block space-y-6">
          <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 dark:text-white">Bộ lọc</h3>
            <button class="text-xs text-primary font-medium hover:underline">
              Xóa tất cả
            </button>
          </div>
          <div class="border-b border-slate-200 dark:border-slate-700 pb-5">
            <h4 class="font-semibold text-sm mb-3 text-slate-800 dark:text-slate-200">
              Ngành nghề
            </h4>
            <div class="space-y-2">
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Công
                  nghệ thông tin</span>
                <span
                  class="ml-auto text-xs text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">42</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span
                  class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Marketing
                  / PR</span>
                <span
                  class="ml-auto text-xs text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">18</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Hành
                  chính nhân sự</span>
                <span
                  class="ml-auto text-xs text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">12</span>
              </label>
            </div>
          </div>
          <div class="border-b border-slate-200 dark:border-slate-700 pb-5">
            <h4 class="font-semibold text-sm mb-3 text-slate-800 dark:text-slate-200">
              Kinh nghiệm
            </h4>
            <div class="space-y-2">
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Thực
                  tập</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input checked=""
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Mới
                  tốt nghiệp</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Nhân
                  viên (1-3 năm)</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 rounded border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  type="checkbox" />
                <span
                  class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Trưởng
                  nhóm / Quản lý</span>
              </label>
            </div>
          </div>
          <div class="pb-5">
            <h4 class="font-semibold text-sm mb-3 text-slate-800 dark:text-slate-200">
              Mức lương
            </h4>
            <div class="space-y-2">
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  name="salary" type="radio" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Tất
                  cả</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  name="salary" type="radio" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">&lt;
                  10 triệu</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  name="salary" type="radio" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">10 -
                  20 triệu</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  name="salary" type="radio" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">&gt;
                  20 triệu</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer group">
                <input
                  class="size-4 border-slate-300 text-primary focus:ring-primary bg-slate-50 dark:bg-slate-800 dark:border-slate-600"
                  name="salary" type="radio" />
                <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Thỏa
                  thuận</span>
              </label>
            </div>
          </div>
        </div>
      </aside>
      <div class="flex-1">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-bold text-slate-900 dark:text-white">
            Việc làm tốt nhất
          </h2>
          <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 dark:text-slate-400">Sắp xếp:</span>
            <select
              class="bg-transparent border-none text-sm font-medium text-slate-900 dark:text-white focus:ring-0 p-0 cursor-pointer">
              <option>Phù hợp nhất</option>
              <option>Mới nhất</option>
              <option>Lương (Cao-Thấp)</option>
            </select>
          </div>
        </div>
        <div id="job-list" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div
            class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
            <div class="flex justify-between items-start mb-4">
              <div
                class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
                <img alt="Spotify Logo" class="object-contain w-full h-full" data-alt="Company logo"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlgkfC4fItQ9RwJs0HZdOJVvxD7Ot7wHb0qP_MU1FXADXbMA7WeUhETWT_b3CWrLN1PX_rkPUKTOTNfRJ6P_g-TB2dYp3-Q85tnySTCResEzM6qCK2OAtJ7iQ1rvUmU5353AM3P9uZwWfCsQwA-5hS_lj1UwvbymumOZnHaVs_pd_trSoIHmelArfl5tQTwq2w6NrQ3rL6tlBr00y8P-0FVdNYBEjb8hfCvA3mIWQJPltfN7L6pddKnAnRT5dalbztE0wyKDZc8CI" />
              </div>
              <button
                class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">favorite</span>
              </button>
            </div>
            <div class="mb-4">
              <h3
                class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                Product Designer
              </h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Spotify Inc.
              </p>
            </div>
            <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">location_on</span>
                <span>Hà Nội</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">attach_money</span>
                <span>30-45 triệu</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">work_history</span>
                <span>3 Năm</span>
              </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-orange-500 flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">hourglass_bottom</span>
                  Hạn nộp: 30/12/2024
                </span>
                <span class="text-xs text-slate-400">Còn 2 ngày</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-orange-400 rounded-full" style="width: 80%"></div>
              </div>
            </div>
          </div>
          <div
            class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
            <div class="flex justify-between items-start mb-4">
              <div
                class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
                <img alt="Google Logo" class="object-contain w-full h-full" data-alt="Company logo"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuCx1brFfXbpqPqMYKFqpIAvkAtIbDDGnU0r58X1CpaXgTYqbpyF0msowX-VOJR4b7HMEbQPFHf9O9YUlDrJhQMjrs_5yoiznr_yRdgQ_TxU2ptAzj8slElj09e37K3JzsOMwUniFjqa1cJBVJyitrnzjpp21_l747kibe8EnqL_olGrzgRUTWSv-WjvGC923P8UI8Y5a-_fxfnFyvM0CJwAuyUbKR24oN13hPicR6o6CUpVZhKkT98Sr3DTe-zEBcpg16YRiV9b4FU" />
              </div>
              <button
                class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">favorite</span>
              </button>
            </div>
            <div class="mb-4">
              <h3
                class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                Senior Frontend Engineer
              </h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Google
              </p>
            </div>
            <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">location_on</span>
                <span>Hà Nội</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">attach_money</span>
                <span>30-45 triệu</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">work_history</span>
                <span>5+ Năm</span>
              </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-primary flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">group</span>
                  Tuyển gấp
                </span>
                <span class="text-xs text-slate-400">120+ Ứng tuyển</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full" style="width: 45%"></div>
              </div>
            </div>
          </div>
          <div
            class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
            <div class="flex justify-between items-start mb-4">
              <div
                class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
                <div
                  class="w-8 h-8 rounded bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold"
                  data-alt="Abstract gradient logo">
                  D
                </div>
              </div>
              <button
                class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">favorite</span>
              </button>
            </div>
            <div class="mb-4">
              <h3
                class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                Data Analyst
              </h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                DataFlow Systems
              </p>
            </div>
            <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">location_on</span>
                <span>Hà Nội</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">attach_money</span>
                <span>30-45 triệu</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">work_history</span>
                <span>2 Năm</span>
              </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-green-600 flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">new_releases</span>
                  Mới đăng
                </span>
                <span class="text-xs text-slate-400">Đăng hôm nay</span>
              </div>
              <div class="w-full h-1 bg-green-100 dark:bg-green-900/30 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full w-full"></div>
              </div>
            </div>
          </div>
          <div
            class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
            <div class="flex justify-between items-start mb-4">
              <div
                class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
                <img alt="Airbnb Logo" class="object-contain w-full h-full" data-alt="Company logo"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuDM5K9-uDIt8klD1gjHjTRr7A08LiG3yuF941HzUG9tFelgh8E00FovxoFvXnx-sZ7BUhxorQ41qdYzH1dbunUdCKwheyS_NnWYvGIF5BLAg3_FNPGsrQr2iTDROgjhRBHZ2EdFxu8lf5jCwU1NOf5LlXA1yPJIMS3Hnp2JkUV2W_NN8Sw0DnHNs6-oOimAfMCT3PbHnEkNCelRIeTd6-39xUwBWvfAV1GiUOVRkanD2JgoWbiLGPNT0DHGC7DzRCMFPck69J840o8" />
              </div>
              <button
                class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">favorite</span>
              </button>
            </div>
            <div class="mb-4">
              <h3
                class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                Marketing Manager
              </h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Airbnb
              </p>
            </div>
            <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">location_on</span>
                <span>Hà Nội</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">attach_money</span>
                <span>30-45 triệu</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">work_history</span>
                <span>4 Năm</span>
              </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">visibility</span>
                  Lượt xem
                </span>
                <span class="text-xs text-slate-400">340 lượt xem</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-slate-400 rounded-full" style="width: 25%"></div>
              </div>
            </div>
          </div>
          <div
            class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
            <div class="flex justify-between items-start mb-4">
              <div
                class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
                <img alt="Stripe Logo" class="object-contain w-full h-full" data-alt="Company logo"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuCMbofpCvGkOTLsnUlraIDdPfIl3AzNPpUdv2sqQf_KAFoysK5HOvN36U0v6eX5OBx-x1RMBGjF-4n6Tnjza6ISsImjTFlkk-C_awHpXXfMKHEtt6wCuWQGRgWhH2G1m03JCk4kP-p1tv3-Q-mCCe7AmMp1Q9vNrcFJD6XsVyyFcCYfjFAw-BNiDWtrdtygiVVZtgyFxP4PKs3cNOj_K-7nEOSIMfrPlIUq-DRrGS_5u0B2LQXhJxNESZ3tVWSjUrS9it_4Jgl_aF8" />
              </div>
              <button
                class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">favorite</span>
              </button>
            </div>
            <div class="mb-4">
              <h3
                class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                Backend Developer (Go)
              </h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Stripe
              </p>
            </div>
            <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">location_on</span>
                <span>Hà Nội</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">attach_money</span>
                <span>30-45 triệu</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">work_history</span>
                <span>Mid-Level</span>
              </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-primary flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">trending_up</span>
                  Nổi bật
                </span>
                <span class="text-xs text-slate-400">Xu hướng</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full" style="width: 90%"></div>
              </div>
            </div>
          </div>
          <div
            class="group relative flex flex-col bg-white dark:bg-card-dark rounded-xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-800 p-5">
            <div class="flex justify-between items-start mb-4">
              <div
                class="h-12 w-12 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center p-2 border border-slate-100 dark:border-slate-700">
                <div
                  class="w-8 h-8 rounded bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center text-white font-bold"
                  data-alt="Abstract gradient logo">
                  F
                </div>
              </div>
              <button
                class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined">favorite</span>
              </button>
            </div>
            <div class="mb-4">
              <h3
                class="font-bold text-lg text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors line-clamp-1">
                HR Specialist
              </h3>
              <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                Future People
              </p>
            </div>
            <div class="flex flex-wrap gap-y-2 gap-x-4 text-xs text-slate-500 dark:text-slate-400 mb-6">
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">location_on</span>
                <span>Hà Nội</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">attach_money</span>
                <span>30-45 triệu</span>
              </div>
              <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-base">work_history</span>
                <span>2 Năm</span>
              </div>
            </div>
            <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
              <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">event</span>
                  Sắp hết hạn
                </span>
                <span class="text-xs text-slate-400">Còn 5 ngày</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-slate-500 rounded-full" style="width: 60%"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-10 flex justify-center">
          <nav aria-label="Pagination" class="flex gap-2">
            <a class="h-10 w-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800"
              href="#">
              <span class="material-symbols-outlined text-sm">chevron_left</span>
            </a>
            <a class="h-10 w-10 flex items-center justify-center rounded-lg bg-primary text-white font-medium shadow-sm shadow-blue-500/30"
              href="#">1</a>
            <a class="h-10 w-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium"
              href="#">2</a>
            <a class="h-10 w-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium"
              href="#">3</a>
            <span class="h-10 w-10 flex items-center justify-center text-slate-400">...</span>
            <a class="h-10 w-10 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800"
              href="#">
              <span class="material-symbols-outlined text-sm">chevron_right</span>
            </a>
          </nav>
        </div>
      </div>
    </main>
    <footer class="bg-white dark:bg-[#111a25] border-t border-slate-200 dark:border-slate-800 pt-16 pb-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          <div>
            <div class="flex items-center gap-2 mb-6">
              <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
                <span class="material-symbols-outlined text-[20px]">work</span>
              </div>
              <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">DDS</span>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">
              Kết nối nhân tài với cơ hội. Chúng tôi giúp bạn tìm được công việc
              hoàn hảo và giúp công ty tìm được nhân viên lý tưởng.
            </p>
            <div class="flex gap-4">
              <a class="text-slate-400 hover:text-primary transition-colors" href="#">
                <span class="sr-only">Facebook</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z">
                  </path>
                </svg>
              </a>
              <a class="text-slate-400 hover:text-primary transition-colors" href="#">
                <span class="sr-only">Twitter</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z">
                  </path>
                </svg>
              </a>
              <a class="text-slate-400 hover:text-primary transition-colors" href="#">
                <span class="sr-only">LinkedIn</span>
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                  <path
                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z">
                  </path>
                </svg>
              </a>
            </div>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">
              Về DDS
            </h3>
            <ul class="space-y-3">
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Giới
                  thiệu</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#">Tuyển dụng</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors"
                  href="#">Blog</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Liên
                  hệ</a>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">
              Ứng viên
            </h3>
            <ul class="space-y-3">
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Tìm
                  việc làm</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Tạo
                  CV</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Tra
                  cứu lương</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Cẩm
                  nang sự nghiệp</a>
              </li>
            </ul>
          </div>
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">
              Nhà tuyển dụng
            </h3>
            <ul class="space-y-3">
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Đăng
                  tin tuyển dụng</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Tìm
                  hồ sơ</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Giải
                  pháp nhân sự</a>
              </li>
              <li>
                <a class="text-sm text-slate-500 dark:text-slate-400 hover:text-primary transition-colors" href="#">Báo
                  giá dịch vụ</a>
              </li>
            </ul>
          </div>
        </div>
        <div
          class="border-t border-slate-100 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-xs text-slate-400 text-center md:text-left">
            © 2024 DDS. All rights reserved.
          </p>
          <div class="flex gap-6">
            <a class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" href="#">Chính sách bảo
              mật</a>
            <a class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" href="#">Điều khoản dịch
              vụ</a>
          </div>
        </div>
      </div>
    </footer>
    <script src="./js/jobs.js"></script>
    <script src="./js/applications.js"></script>
    <script src="./js/auth.js"></script>
  </body>

</html>