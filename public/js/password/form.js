$(document).ready(function() {
    $('.toggle-password').on('click', function() {
        const targetId = $(this).data('target');
        const targetInput = $('#' + targetId);
        const icon = $(this).find('i');
        
        if (targetInput.attr('type') === 'password') {
            targetInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            targetInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

})