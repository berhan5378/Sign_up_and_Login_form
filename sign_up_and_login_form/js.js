const btn_login = document.querySelector(".btn_login"),
        btn_sign_up = document.querySelector(".btn_sign_up"),
        sign_up = document.querySelector(".sign_up"),
        login = document.querySelector(".login"),
        login_container_wrapper = document.querySelector(".login-container .wrapper"),
        login_container = document.querySelector(".login-container"),
        svg_text = document.querySelector(".sign_up-container svg text"),
        svg_text1 = document.querySelector(".login-container svg text"),
        sign_up_container_wrapper = document.querySelector(".sign_up-container .wrapper");

        btn_login.onclick = function() {
            login_container.style.zIndex = '4'; 
            sign_up_container_wrapper.style.opacity ='0';
            login_container_wrapper.style.opacity ='1'; 
            svg_text1.style.animation='stroke 2s alternate'; 
            login.style.animation='moveRight 1.2s forwards'; 
            sign_up.style.animation='hide_right 5.2s forwards';   
      };

      btn_sign_up.onclick = function() {
            login_container.style.zIndex = '1';
            sign_up_container_wrapper.style.opacity ='1';
            login_container_wrapper.style.opacity ='0';
            svg_text.style.animation='stroke 2s alternate'; 
            sign_up.style.animation='moveleft 1.2s forwards'; 
            login.style.animation='hide_left 5.2s forwards';   
      };