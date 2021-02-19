$('.form--ajax').submit(function () {
    console.log('form fired');
    $('body').addClass('overflow-hidden').append($('<div class="fixed bg-black-darker bg-opacity-90 w-full h-full top-0 left-0 z-50"><div class="absolute m-auto inset-0 h-48 w-48 opacity-50 z-10  "><span class="cog1 absolute animate-spin-vslow w-full h-full bg-contain bg-no-repeat"></span><span  class="cog2 absolute animate-spin-slow w-full h-full bg-contain bg-no-repeat"></span></div></div>'));

    $form = $(this);

    var formRecap = $form.data('recaptcha');

    event.preventDefault();

    // CLASS FOR LOADING FEEDBACK
    // $('html').addClass('loading');

    // FOR DATA TRANSFERRING, ONLY USE THESE ELEMENTS
    var $inputs = $(' :input[type="text"], :input[type="email"], :input[type="tel"], :input[type="hidden"], select', this);


    var values = {};

    // CREATE ARRAY OF INPUT
    $inputs.each(function () {
        values[this.name] = $(this).val();
        console.log(this.name);
    });


    $form = $(this);


    var formData = new FormData();


    // Attach file
    formData = new FormData($(this)[0]);

    var formRecap = $form.data('recaptcha');

    event.preventDefault();

    // needs for recaptacha ready
    grecaptcha.ready(function () {
        $('.preloader').removeClass('d-none');

        // do request for recaptcha token
        // response is promise with passed token
        var recapt = $(':input[name="RECAPT_SITE"]').val();

        grecaptcha.execute(recapt, {
            action: formRecap // used for dashboard analytics to ID any problems
        }).then(function (token) {
            // add token to form


            // REMOVE FEEDBACK CLASS
            // $('html').removeClass('loading');
            formData.append('token', token);
            formData.append("action", "ee__form_validate");


            $.ajax({
                type: "POST",
                url: wpAdmin.ajaxurl,
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                complete: function (result) {
                    console.log(result);
                    if (result.success) {
                        $form.get(0).reset();

                        // triggerThankYou();
                        window.location.href = "/submission-successful/";

                    } else {
                        window.location.href = "/error/";

                    }
                }
            })
        });
    });
});