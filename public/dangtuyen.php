<!doctype html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Đăng Tin Tuyển Dụng - DDS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
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
                },
            },
        };
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display flex flex-col min-h-screen text-slate-900 dark:text-slate-100 transition-colors duration-200">
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-[#111a25]/95 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="timkiem.php" class="flex items-center gap-2">
                    <div class="flex items-center justify-center size-8 rounded bg-primary text-white">
                        <span class="material-symbols-outlined text-[20px]">work</span>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">DDS</span>
                </a>
                <div id="auth-buttons" class="flex items-center gap-3">
                    <!-- Auth buttons will be injected by auth.js -->
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto bg-white dark:bg-card-dark p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="mb-8 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Đăng tin tuyển dụng mới</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Nhập thông tin chi tiết về vị trí bạn đang tìm kiếm.</p>
            </div>
            
            <form id="postJobForm" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tiêu đề công việc <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white focus:ring-primary focus:border-primary" placeholder="Ví dụ: Senior Frontend Developer">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tên công ty <span class="text-red-500">*</span></label>
                        <input type="text" name="company" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white focus:ring-primary focus:border-primary" placeholder="Ví dụ: Tech Corp">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Địa điểm <span class="text-red-500">*</span></label>
                        <input type="text" name="location" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white focus:ring-primary focus:border-primary" placeholder="Ví dụ: Hà Nội">
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mức lương <span class="text-red-500">*</span></label>
                        <input type="text" name="salary" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white focus:ring-primary focus:border-primary" placeholder="Ví dụ: 20 - 30 triệu hoặc Thỏa thuận">
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Mô tả công việc</label>
                        <textarea name="description" rows="6" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-[#0f1723] text-slate-900 dark:text-white focus:ring-primary focus:border-primary" placeholder="Mô tả chi tiết, yêu cầu, quyền lợi..."></textarea>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="timkiem.php" class="px-5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Hủy bỏ</a>
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">Đăng tin ngay</button>
                </div>
            </form>
        </div>
    </main>

    <script src="./js/auth.js"></script>
    <script src="./js/jobs.js"></script>
    <script>
        // Kiểm tra đăng nhập ngay khi vào trang
        document.addEventListener("DOMContentLoaded", () => {
            const token = localStorage.getItem("token");
            if (!token) {
                alert("Bạn cần đăng nhập để đăng tin tuyển dụng!");
                window.location.href = "dangnhap.php";
            }
        });

        document.getElementById("postJobForm").addEventListener("submit", createJob);
    </script>
</body>
</html>