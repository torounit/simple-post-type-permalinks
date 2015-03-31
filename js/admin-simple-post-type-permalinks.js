(function($){
    $(".sptp-fieldset").each(function(){

        var $fieldset = $(this);
        var $customInput = $fieldset.find('input[type=text]');
        $fieldset.find('input[type=radio]').change(function() {
            $customInput.val( $(this).val());
        });
    });

}(jQuery));