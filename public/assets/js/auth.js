/*
========================================================================
   BOOTSTRAP 5 ADMIN TEMPLATE - SPARK ADMIN
   AUTHENTICATION MODULE JAVASCRIPT
   Developed with premium UI/UX standards

   Template Name: Spark Admin
   Version: 1.0
   Author: Spark Admin Team
   Email: hello.sparkadmin@gmail.com
   URL: https://sparkadmin.web.id
========================================================================
*/

document.addEventListener("DOMContentLoaded", function () {
    /**
     * Password Visibility Toggle Logic
     * Enables toggling password input between obscured dots and plain text.
     */
    document
        .querySelectorAll(".password-toggle-btn")
        .forEach(function (button) {
            button.addEventListener("click", function () {
                const targetId = this.getAttribute("data-target");
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector("i");

                if (!passwordInput) {
                    return;
                }

                const isPassword = passwordInput.type === "password";

                // Toggle password visibility
                passwordInput.type = isPassword ? "text" : "password";

                // Toggle icon
                if (isPassword) {
                    icon.classList.remove("bi-eye");
                    icon.classList.add("bi-eye-slash");
                    this.setAttribute("aria-label", "Hide password");
                } else {
                    icon.classList.remove("bi-eye-slash");
                    icon.classList.add("bi-eye");
                    this.setAttribute("aria-label", "Show password");
                }
            });
        });

    /**
     * Client-Side Form Validation Logic
     * Prevents submission if form inputs are invalid and applies Bootstrap validation styles.
     */
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            if (!loginForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            loginForm.classList.add("was-validated");
        });
    }
});
