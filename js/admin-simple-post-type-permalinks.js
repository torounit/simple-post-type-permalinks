(function($){
    $(".sptp-fieldset").each(function(){

        var $fieldset = $(this);
        var $customInput = $fieldset.find('input[type=text]');
        $fieldset.find('input[type=radio]').change(function() {
            var val = $(this).val();
            if( val != 'custom') {
                $customInput.val( val );
            }

        });
    });

}(jQuery));