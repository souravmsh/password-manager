<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" /> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Password Policy</title>
    <style type="text/css">
.nav-flush .nav-link {
  border-radius: 0;
}

.btn-toggle {
  display: inline-flex;
  align-items: center;
  padding: .25rem .5rem;
  font-weight: 600;
  color: rgba(0, 0, 0, .65);
  background-color: transparent;
  border: 0;
}
.btn-toggle:hover,
.btn-toggle:focus {
  color: rgba(0, 0, 0, .85);
  background-color: #d2f4ea;
}

.btn-toggle::before {
  width: 1.25em;
  line-height: 0;
  content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280,0,0,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
  transition: transform .35s ease;
  transform-origin: .5em 50%;
}

.btn-toggle[aria-expanded="true"] {
  color: rgba(0, 0, 0, .85);
}
.btn-toggle[aria-expanded="true"]::before {
  transform: rotate(90deg);
}

.btn-toggle-nav a {
  display: inline-flex;
  padding: .1875rem .5rem;
  margin-top: .125rem;
  margin-left: 1.25rem;
  text-decoration: none;
}
.btn-toggle-nav a:hover,
.btn-toggle-nav a:focus {
  background-color: #d2f4ea;
}

.scrollarea {
  overflow-y: auto;
}
    </style>
</head>
<body>

    <div class="container my-3">
        <div class="row">
            <div class="col-sm-3">
            <div class="flex-shrink-0 p-3 bg-white" style="width: 280px;">
                <a href="{{ route('password-manager.expiry') }}" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom">
                  <svg class="bi me-2" width="30" height="24"><use xlink:href="#bootstrap"></use></svg>
                  <h1 class="fs-4 fw-bold">Password Policy</h1>
                </a>
                <ul class="list-unstyled ps-0">
                  <li class="mb-1">
                    <button class="btn btn-toggle align-items-center rounded" data-bs-toggle="collapse" data-bs-target="#password-manager-collapse" aria-expanded="false">
                      Menu
                    </button>
                    <div class="collapse show" id="password-manager-collapse" style="">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li>
                            <a href="{{  route('password-manager.expiry')  }}" class="link-dark rounded"> User Expiration</a>
                        </li>
                        <li> 
                            <a href="{{  route('password-manager.pretend.show')  }}" data-modal="ajaxifyModal" class="link-dark rounded"> Pretend Login</a>
                        </li>
                        <li>
                            <a href="{{  route('password-manager.rules')  }}" class="link-dark rounded"> Password Rules</a>
                        </li> 
                        <li>
                            <a href="{{  route('password-manager.checklist')  }}" class="link-dark rounded"> Password Checklist</a>
                        </li> 
                      </ul>
                    </div>
                  </li> 
                  <li class="border-top my-3"></li> 
                </ul>
            </div>
            </div>

            <div class="col-sm-9">
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.all.min.js" integrity="sha256-nk6ExuG7ckFYKC1p3efjdB14TU+pnGwTra1Fnm6FvZ0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".select2").select2({
                theme: "bootstrap-5" 
            });

            $(".modal .select2").select2({
                theme: "bootstrap-5",
                dropdownParent: $('.modal')
            });
        });


        function sweetAlert(icon = 'success', title = 'success', message = '', reload=false) {
            Swal.fire({
              icon: icon,
              title: title,
              html: message,
            }).then((result) => {
              if (result.isConfirmed && reload) {
                window.location.reload();
              }
            });
        }

        function sweetToast(icon = 'success', title = 'success', message = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({icon:icon, title:title, html:message});
        }


        $(document).on("click", '[data-modal="ajaxifyModal"]', function(e) {
            e.preventDefault();
            $("#ajaxifyModal").empty();
            var btn  = $(this);
            var url  = btn.data("remote") || btn.attr("route") || btn.attr("href");
            $("body").append(`<div class="modal fade" id="ajaxifyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"></div>`); 
         
            $('#ajaxifyModal').load(url, function(){
                var bsModal = new bootstrap.Modal(document.getElementById('ajaxifyModal'));
                bsModal.show();
            });    

            $('#ajaxifyModal').on('shown.bs.modal', function (e) {
                $(this).find('.select2').select2({
                    theme: "bootstrap-5",
                    dropdownParent: $(this).find('.modal-content')
                });
            });
        });

          
        $(document).on('submit', '.ajaxifyForm', function (e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(form[0]);

            $.ajax({
                url        : form.attr('action'),
                type       : form.attr('method'),
                dataType   : 'json',
                headers    : {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content'),
                    'Accept'       : 'application/json',
                    'Authorization': 'Bearer ' + '{{ auth()->user()->api_token ?? "" }}',
                },
                cache       : false,
                contentType : false,
                processData : false,
                data:  formData,
                beforeSend  : function() {
                    form.find('label > span i.text-help').remove();
                    form.find('input, select, textarea').addClass('is-valid').removeClass('is-invalid');
                },
                success     : function(response)
                {
                    if (response.status) {
                        sweetAlert('success', response.message);

                        setTimeout(function() {
                            $('#ajaxifyModal').modal('hide');
                            window.location.href = response.redirect;
                        }, 2000);
                    } else {
                        sweetAlert('error', response.message); 
                        form.find('button[type=submit]').html('<i class="fa fa-refresh"></i> Try again</span>');
                    }
                },
                error       : function(xhr)
                {
                    if( xhr.status === 422 ) { 
                        var errors = $.parseJSON(xhr.responseText);
                        var html = "<ul>"; 
                        $.each(errors.errors, function (name, message) {
                            if (name.includes('.')) {
                                var field = name.split('.');
                                name = field[0]+'['+field[1]+']'
                            }
                            form.find('[name="'+name+'"]').addClass('is-invalid').removeClass('is-valid').parent().find('label > span').append('<i class="text-help">'+message[0]+'</i>');
                            html += `<li>${message[0]}</li>`;
                        }); 
                        html += '</ul>';
                        sweetToast('warning', errors.message, html); 
                    } else {
                        sweetAlert('error', xhr.statusText); 
                    }
                    form.find('button[type=submit]').html('<i class="fa fa-refresh"></i> Try again</span>');
                }
            }); 
        }); 
    </script>
  </body>
</html> 




