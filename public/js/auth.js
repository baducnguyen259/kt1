const API_BASE = "/webkiemthu/api";

const registerForm = document.getElementById("registerForm");
if (registerForm) {
  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    console.log("Đang gửi yêu cầu đăng ký...");

    const formData = new FormData(e.target);
    const data = {
      name: formData.get("fullname") || formData.get("name") || "",
      email: formData.get("email") || "",
      password: formData.get("password") || "",
      role: "seeker",
    };
    try {
      const res = await fetch(`${API_BASE}/auth.php?action=register`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      // Đọc text trước để tránh lỗi nếu server trả về HTML (lỗi PHP)
      const text = await res.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (err) {
        console.error("Lỗi phản hồi từ server:", text);
        throw new Error("Server trả về lỗi không xác định (xem console).");
      }

      if (res.ok) {
        alert("Đăng ký thành công! Đang chuyển hướng đến trang đăng nhập...");
        window.location.href = "dangnhap.php";
      } else {
        alert(result.message || "Đăng ký thất bại");
      }
    } catch (error) {
      console.error("Lỗi:", error);
      alert("Lỗi: " + error.message);
    }
  });
}

const loginForm = document.getElementById("loginForm");
if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
      email: formData.get("email") || "",
      password: formData.get("password") || "",
    };

    try {
      const res = await fetch(`${API_BASE}/auth.php?action=login`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const text = await res.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (err) {
        console.error("Lỗi phản hồi server:", text);
        throw new Error("Lỗi hệ thống: Server trả về dữ liệu không hợp lệ.");
      }

      if (res.ok && result.token) {
        localStorage.setItem("token", result.token);
        alert("Đăng nhập thành công!");
        window.location.href = "timkiem.php";
      } else {
        alert(result.message || "Đăng nhập thất bại");
      }
    } catch (error) {
      console.error("Lỗi đăng nhập:", error);
      alert(error.message || "Có lỗi xảy ra khi kết nối đến máy chủ.");
    }
  });
}

function checkAuth() {
  const token = localStorage.getItem("token");
  const authButtons = document.getElementById("auth-buttons");

  const path = window.location.pathname;
  if (token && (path.includes("dangnhap.php") || path.includes("dangky.php"))) {
    window.location.href = "timkiem.php";
    return;
  }

  if (token && authButtons) {
    authButtons.innerHTML = `
        <a href="dangtuyen.php" class="hidden sm:flex h-9 px-4 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors mr-2">
            Đăng tin
        </a>
        <a href="trangthai.php" class="hidden lg:block text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary mr-2">
            Hồ sơ của tôi
        </a>
        <button id="logoutBtn" class="h-9 px-4 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-bold hover:bg-slate-200 transition-colors">
            Đăng xuất
        </button>
    `;
    document.getElementById("logoutBtn").addEventListener("click", () => {
      localStorage.removeItem("token");
      window.location.href = "dangnhap.php";
    });
  }
}

document.addEventListener("DOMContentLoaded", checkAuth);
