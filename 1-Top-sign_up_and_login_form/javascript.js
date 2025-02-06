const btn_login = document.querySelector(".btn_login"),
      btn_sign_up = document.querySelector(".btn_sign_up");

      btn_login.onclick = function() { 
        document.querySelector('.container').classList.add('active-login'); 
  };

  btn_sign_up.onclick = function() { 
    document.querySelector('.container').classList.remove('active-login');
  };