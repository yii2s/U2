// Generated by CoffeeScript 1.6.3
jQuery(function($) {
  var Register;
  Register = {
    mask: $('div.mini-mask'),
    pops: $('.mini-pop'),
    changePWBtn: $('a.mini-changepw-btn'),
    changePW: $('form.mini-change-password'),
    changeSucc: $('div.mini-change-succ'),
    register: $('form.mini-register'),
    tip: $('p.mini-control-tips'),
    regBtn: $('a.mini-reg-btn'),
    loginBtn: $('a.mini-login-btn'),
    login: $('form.mini-login'),
    logout: $('a.mini-logout'),
    fetch: $('form.mini-fetch'),
    fetchSucc: $('div.mini-fetch-succ'),
    checkPhone: $('#checkbox-phone'),
    regPhone: $('#reg-phone'),
    checkNone: $('#checkbox-none'),
    clauseBtn: $('a.mini-clause-btn'),
    clause: $('div.mini-clause'),
    closeMask: function() {
      this.mask.hide();
      return this;
    },
    showMask: function() {
      this.mask.show();
      return this;
    },
    closeAll: function() {
      Register.closeMask().pops.hide();
      clearInterval($.uniqlo.timerFind1);
      clearInterval($.uniqlo.timerFind2);
      return Register.hideTip();
    },
    hideTip: function() {
      return Register.tip.html('');
    }
  };
  $.Register = Register;
  Register.checkPhone.on('click', function() {
    if (this.checked) {
      return Register.regPhone.removeClass('hide');
    } else {
      return Register.regPhone.addClass('hide');
    }
  });
  Register.checkNone.on('click', function() {
    if (this.checked) {
      return Register.regPhone.addClass('hide');
    } else {
      return Register.regPhone.removeClass('hide');
    }
  });
  Register.regBtn.on('click', function() {
    return Register.showMask().register.show();
  });
  Register.loginBtn.on('click', function() {
    return Register.showMask().login.show();
  });
  Register.clauseBtn.on('click', function() {
    return Register.clause.show();
  });
  Register.clause.on('click', 'button', function() {
    return Register.clause.hide();
  });
  Register.changePWBtn.on('click', function() {
    var left, offset, that, top;
    that = $(this);
    offset = that.position();
    left = offset.left;
    top = offset.top;
    return Register.changePW.css({
      'left': left + 72,
      'top': top - 5
    }).show();
  });
  Register.changePW.on('click', 'a.mini-change-close', function() {
    Register.changePW.hide();
    return Register.hideTip();
  });
  Register.changeSucc.on('click', 'button', function() {
    Register.changePW.hide();
    Register.changeSucc.hide();
    return Register.hideTip();
  });
  Register.register.on('click', 'a.mini-reg-login', function() {
    Register.register.hide();
    Register.login.show();
    return Register.hideTip();
  });
  Register.login.on('click', 'a.mini-login-reg', function() {
    Register.login.hide();
    Register.register.show();
    return Register.hideTip();
  }).on('click', 'a.mini-login-fetch', function() {
    Register.login.hide();
    Register.fetch.show();
    return Register.hideTip();
  });
  Register.fetch.on('click', 'a.mini-fetch-back', function() {
    Register.fetch.hide();
    Register.login.show();
    Register.fetchSucc.hide();
    return Register.hideTip();
  });
  Register.fetchSucc.on('click', 'button', function() {
    Register.fetchSucc.hide();
    Register.closeMask().fetch.hide();
    return Register.hideTip();
  });
  Register.mask.on('click', Register.closeAll);
  $(document).keydown(function(e) {
    e.which === 27 && Register.closeAll();
  });
});
