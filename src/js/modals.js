$('.openForm').click(function(e){
    $('.panel-expand').removeClass('active');
    $('.panel-expand').hide();
    $('#coverLight').fadeOut(500);
    e.preventDefault();
    openModal('form');
});

// kill iframe on close
$('#videoModalOuter').on('.modal', function () {
    $('#videoModalOuter .modal-content .modal-body iframe').attr('src','');
    $(this).find('#videoModal .modal-body').html('');
});

// kill iframe on close
$('#videoModalOuter').on('.modal', function () {
    $('#videoModalOuter .modal-content .modal-body iframe').attr('src','');
    $('#videoModalOuter').find('#cover', '#videoModalOuter').html('');
});

$('#guaranteeModalOuter').on('.modal', function () {
    $('#guaranteeModalOuter .modal-content .modal-body ').css('overflow-y', 'auto');
    $('#guaranteeModalOuter .modal-content .modal-body').css('max-height', $(window).height() * 0.7);
});

$('.guaranteePopup').click(function(e){
    e.preventDefault();
    openModal('guarantee');
    $('#guaranteeModalOuter .modal-content .modal-body ').css('overflow-y', 'auto');
    $('#guaranteeModalOuter .modal-content .modal-body').css('max-height', $(window).height() * 0.7);
});

$('.termsPopup').click(function(e){
    e.preventDefault();
    openModal('terms');
    $('#termsModalOuter .modal-content .modal-body ').css('overflow-y', 'auto');
    $('#termsModalOuter .modal-content .modal-body').css('max-height', $(window).height() * 0.7);
});

$('.privacyPopup').click(function(e){
    e.preventDefault();
    openModal('privacy');
    $('#privacyModalOuter .modal-content .modal-body ').css('overflow-y', 'auto');
    $('#privacyModalOuter .modal-content .modal-body').css('max-height', $(window).height() * 0.7);
});

$('.complaintsPopup').click(function(e){
    e.preventDefault();
    openModal('complaints');
    $('#complaintsModalOuter .modal-content .modal-body ').css('overflow-y', 'auto');
    $('#complaintsModalOuter .modal-content .modal-body').css('max-height', $(window).height() * 0.7);
});

$('.close').click(function(e){
    e.preventDefault();
    closeModal('video');
    closeModal('image');
    closeModal('form');
    closeModal('quote');
    closeModal('remarket');
    closeModal('guarantee');
    closeModal('terms');
    closeModal('privacy');
    closeModal('complaints');
});

$('a.play').click(function(e){
    e.preventDefault();
    if(!$(this).hasClass('big')) {
        openModal('video');
        $('.panel-expand').removeClass('active');
        $('.panel-expand').hide();
        $('#coverLight').fadeOut(500);

        $('#videoModalOuter .modal-body').html($(this).data('iframe'));
    }
});