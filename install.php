<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<title>Setup your instance</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>UpForm</title>
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700'>
<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css'>
<script src="assets/js/fileuploader.js"></script>
<style>
/* PAGE & INPUT STYLING */
body {
  background: #4792F5;
  color: #FFF;
}

.btn {
  border: 1px solid #FAAF46;
  background-color:#FAAF46;
  display: inline-block;
  padding: 5px 10px;
  font-size: 20px;
  position: relative;
  text-align: left;
  border-radius: 3px;
  -webkit-transition: background 600ms ease, color 600ms ease;
  transition: background 600ms ease, color 600ms ease;
}
.btn span {
  border: 1px solid #FAAF46;
  display: inline-block;
  padding: 1px 6px;
  font-size: 12px;
  border-radius: 5px;
  vertical-align: middle;
  text-align: center;
  margin-top: -5px;
}

input[type="radio"].toggle {
  display: none;
}
input[type="radio"].toggle + label {
  cursor: pointer;
  min-width: 80px;
}
input[type="radio"].toggle + label:hover {
  background: none;
  color: #FFF;
}
input[type="radio"].toggle + label:after {
  content: "";
  height: 100%;
  position: absolute;
  top: 0;
  -webkit-transition: left 100ms cubic-bezier(0.77, 0, 0.175, 1);
  transition: left 100ms cubic-bezier(0.77, 0, 0.175, 1);
  width: 100%;
  z-index: -1;
}
input[type="radio"].toggle.toggle-left + label {
}
input[type="radio"].toggle.toggle-left + label:after {
  left: 100%;
}
input[type="radio"].toggle.toggle-right + label {
  margin-left: 10px;
}
input[type="radio"].toggle.toggle-right + label:after {
  left: -100%;
}
input[type="radio"].toggle:checked + label {
  background: #FFF;
  cursor: default;
  color: #4792F5;
}
input[type="radio"].toggle:checked + label:after {
  left: 0;
}

/* ENDS */

/* UPFORM STYLE STARTS*/
.upform input:focus, select:focus, textarea:focus, button:focus {
  outline: none;
  border-color: #212529 !important;
}
.upform input, select, textarea {
  background-color: #4792F5 !important;
  color: #FFF;
}
.upform {
  font-family: 'Open Sans', sans-serif;
  -webkit-touch-callout: none; /* iOS Safari */
  -webkit-user-select: none; /* Safari */
  -khtml-user-select: none; /* Konqueror HTML */
  -moz-user-select: none; /* Firefox */
  -ms-user-select: none; /* Internet Explorer/Edge */
  user-select: none; /* Non-prefixed version, currently
                supported by Chrome and Opera */
  max-width: 900px;
  margin: 300px auto;
  margin-bottom: 500px;
  padding: 0 20px;
}

.upform .upform-main {
}
.upform .upform-main .input-block {
  padding: 30px 0;
  opacity: 0.25;
  cursor: default;
}
.upform .upform-main .input-block .label {
  display: block;
  font-size: 1.1em;
  line-height: 30px;
}
.upform .upform-main .input-block .input-control {
  margin: 20px 0;
}
.upform .upform-main .input-block .input-control input[type=text] {
  border: none;
  outline-width: 0;
  border-bottom: 2px solid #fff;
  width: 100%;
  font-size: 35px;
  padding-bottom: 10px;
}

.upform .upform-main .input-block.active {
  opacity: 1;
}

.upform .upform-footer {
  margin-top: 60px;
}
.upform .upform-footer .btn {
  font-size: 24px;
  font-weight: bold;
  padding: 5px 20px;
}

input::-webkit-input-placeholder {/* Chrome/Opera/Safari/Edge */
  color:#fff;
}

input::-ms-input-placeholder { /* Microsoft Edge */
  color:#fff;
}

input:-ms-input-placeholder {/* IE 10+ */
  color:#fff;
}
/* UPFORM STYLE ENDS*/
</style>
<script>
  window.console = window.console || function(t) {};
</script>
<script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize", "*");
  }
</script>
</head>
<body translate="no">
<div class="upform">
<form>
<div class="upform-header"></div>
<div class="upform-main">
<div class="input-block">
<div class="label">What's your instance called</div>
<div class="input-control">
<input type="text" class="required" autocomplete="off">
</div>
</div>
<div class="input-block">
<div class="label">Upload your logo</div>
<div class="input-control">
<input type="file" id="logo" name="logo" accept="image/png, image/jpeg">
</div>
</div>
<div class="input-block">
<div class="label">Choose a background for your login screen</div>
<div class="input-control">

</div>
</div>

<div class="input-block">
<div class="label">Color Scheme</div>
<div class="input-control">
<select id="monselect">
  <option value="blue">Blue</option> 
  <option value="blue-dark">blue-dark</option>
  <option value="default-dark">default-dark</option>
  <option value="green">green</option>
  <option value="green-dark">green-dark</option>
  <option value="megna">megna</option>
  <option value="megna-dark">megna-dark</option>
  <option value="purple">purple</option>
  <option value="purple-dark">purple-dark</option>
  <option value="red">red</option>
  <option value="red-dark">red-dark</option>
</select>
</div>
</div>


<div class="input-block">
<div class="label">Setup your admin user</div>
<div class="input-control">
<input type="text" class="required" autocomplete="off" placeholder="Email">
<input type="text" class="required" autocomplete="off" placeholder="Password">
</div>
</div>








</div>
<div class="upform-footer">
<button type="submit" class="btn btn-primary btn-lg">Submit</button>
</div>
</form>
</div>
<script src="https://static.codepen.io/assets/common/stopExecutionOnTimeout-157cd5b220a5c80d4ff8e0e70ac069bffd87a61252088146915e8726e5d9f147.js"></script>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.2/jquery.scrollTo.min.js'></script>
<script id="rendered-js">
$.fn.upform = function () {
  var $this = $(this);
  var container = $this.find(".upform-main");

  $(document).ready(function () {
    $(container).find(".input-block").first().click();
  });

  $($this).find("form").submit(function () {
    return false;
  });

  $(container).
  find(".input-block").
  not(".input-block input").
  on("click", function () {
    rescroll(this);
  });

  $(container).find(".input-block input").keypress(function (e) {
    if (e.which == 13) {
      if ($(this).hasClass("required") && $(this).val() == "") {
      } else moveNext(this);
    }
  });

  $(container).find('.input-block input[type="radio"]').change(function (e) {
    moveNext(this);
  });

  $(window).on("scroll", function () {
    $(container).find(".input-block").each(function () {
      var etop = $(this).offset().top;
      var diff = etop - $(window).scrollTop();

      if (diff > 100 && diff < 300) {
        reinitState(this);
      }
    });
  });

  function reinitState(e) {
    $(container).find(".input-block").removeClass("active");

    $(container).find(".input-block input").each(function () {
      $(this).blur();
    });
    $(e).addClass("active");
    /*$(e).find('input').focus();*/
  }

  function rescroll(e) {
    $(window).scrollTo($(e), 200, {
      offset: { left: 100, top: -200 },
      queue: false });

  }

  function reinit(e) {
    reinitState(e);
    rescroll(e);
  }

  function moveNext(e) {
    $(e).parent().parent().next().click();
  }

  function movePrev(e) {
    $(e).parent().parent().prev().click();
  }
};

$(".upform").upform();
$.fn.upform = function () {
  var $this = $(this);
  var container = $this.find(".upform-main");

  $(document).ready(function () {
    $(container).find(".input-block").first().click();
  });

  $($this).find("form").submit(function () {
    return false;
  });

  $(container).
  find(".input-block").
  not(".input-block input").
  on("click", function () {
    rescroll(this);
  });

  $(container).find(".input-block input").keypress(function (e) {
    if (e.which == 13) {
      if ($(this).hasClass("required") && $(this).val() == "") {
      } else moveNext(this);
    }
  });

  $(container).find('.input-block input[type="radio"]').change(function (e) {
    moveNext(this);
  });

  $(window).on("scroll", function () {
    $(container).find(".input-block").each(function () {
      var etop = $(this).offset().top;
      var diff = etop - $(window).scrollTop();

      if (diff > 100 && diff < 300) {
        reinitState(this);
      }
    });
  });

  function reinitState(e) {
    $(container).find(".input-block").removeClass("active");

    $(container).find(".input-block input").each(function () {
      $(this).blur();
    });
    $(e).addClass("active");
    /*$(e).find('input').focus();*/
  }

  function rescroll(e) {
    $(window).scrollTo($(e), 200, {
      offset: { left: 100, top: -200 },
      queue: false });

  }

  function reinit(e) {
    reinitState(e);
    rescroll(e);
  }

  function moveNext(e) {
    $(e).parent().parent().next().click();
  }

  function movePrev(e) {
    $(e).parent().parent().prev().click();
  }
};

$(".upform").upform();
//# sourceURL=pen.js
    </script>
</body>
</html>
