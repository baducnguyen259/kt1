<?php
// File: suatin.php
?>
<!Doctype html>
<html class="light" lang="vi">

  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Sửa tin tuyển dụng - DDS</title>
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
        },
      },
    };
    </script>
    <style>
    body {
      font-family: "Inter", sans-serif;
    }
    </style>
  </head>

  <body
    class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display text-[#0d131c] dark:text-gray-200">

    <!-- Header -->
    <?php include_once './includes/header.php'; ?>

    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Sửa tin tuyển dụng</h1>
        <form id="edit-job-form"
          class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border border-border-color dark:border-gray-700 space-y-6">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiêu đề công
              việc</label>
            <input type="text" name="title" id="title" required
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
          </div>
          <div>
            <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên công
              ty</label>
            <input type="text" name="company" id="company" required
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
          </div>
          <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Địa
              điểm</label>
            <input type="text" name="location" id="location" required
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
          </div>
          <div>
            <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mức
              lương</label>
            <input type="text" name="salary" id="salary" required
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
          </div>
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mô tả công
              việc</label>
            <textarea name="description" id="description" rows="8"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary"></textarea>
          </div>
          <div class="pt-4">
            <button type="submit"
              class="h-11 px-8 flex items-center justify-center rounded-lg bg-primary text-white text-sm font-bold hover:bg-blue-600 transition-colors shadow-sm w-full sm:w-auto">
              Lưu thay đổi
            </button>
          </div>
        </form>
      </div>
    </main>

    <!-- Footer -->
    <?php include_once './includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', async function() {
      const urlParams = new URLSearchParams(window.location.search);
      const jobId = urlParams.get('id');
      if (!jobId) {
        alert('Không tìm thấy ID công việc.');
        window.location.href = 'quanlytindang.php';
        return;
      }

      // Tải dữ liệu cũ của công việc
      const res = await fetch(`/webkiemthu/api/jobs.php?id=${jobId}`);
      const job = await res.json();

      document.getElementById('title').value = job.title;
      document.getElementById('company').value = job.company;
      document.getElementById('location').value = job.location;
      document.getElementById('salary').value = job.salary;
      document.getElementById('description').value = job.description;

      // Gắn sự kiện submit
      document.getElementById('edit-job-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        updateJob(e, jobId);
      });
    });
    </script>
    <script src="./js/auth.js"></script>
    <script src="./js/utils.js"></script>
    <script src="./js/jobs.js"></script>
  </body>

</html>