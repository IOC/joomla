(function($) {
    $(document).ready(function() {
        $('#contactStudy').removeAttr('required');
        $('#tipusID').removeAttr('required');
        $('#contactiocForm').append('<input type="hidden" name="script" value="true">');
    });
    $(document).on('submit', '#contactiocForm', function(e) {
        e.preventDefault();
        var url = $(this).attr('action');
        var info = new FormData(document.getElementById('contactiocForm'));
        var oReq = new XMLHttpRequest();
        var submit = $('.contact-submit').text();
        if (validateForm()) {
            $('.contact-submit').attr('disabled', 'disabled').text('').append('<span class="contact-loader"></span>');
            oReq.open("POST", url, true);
            oReq.onload = function(oEvent) {
                if (oReq.status == 200) {
                    var data = $.parseJSON(oReq.response);
                    $('#contactError').remove();
                    $('.contact-submit').removeAttr('disabled').empty().text(submit);
                    if (!data.error) {
                        $('#contactioc-page').slideToggle("slow", function() {
                            $(this).empty().append(data.html).slideToggle();
                        });
                    } else {
                        $('#contactioc-page').append(data.html);
                    }
                } else {
                    var node = '<div class="alert alert-danger alert-dismissible" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '<h4>' +
                            Joomla.JText._('COM_CONTACTIOC_ERROR') +
                        '</h4>' +
                        '<p>' +
                            'Error oReq.status occurred.' +
                        '</p>' +
                    '</div>';
                    $('#contactioc-page').append(node);
                }
            };
            oReq.send(info);
        }
    });
    $(document).on('click', '#contactiocForm .contact-submit', function(e) {
        $.each($('#contactiocForm .form-control'), function(index, value) {
            if (value.validity.valueMissing || value.validity.patternMismatch || value.validity.typeMismatch) {
                var position = $(value).position();
                $("html, body").animate({ scrollTop: position.top - 90}, 800);
                return false;
            }
        })
    });
    $(document).on('click', '.chzn-results .active-result', function() {
        if ($(this).index() > 0) {
            $(this).closest('.chzn-container').find('a').removeClass('input-required');
            removeErrorMessage($(this).closest('.chzn-container').find('.clarification.error'));
        } else {
            $(this).closest('.chzn-container').find('a').addClass('input-required');
            addErrorMessage($(this).closest('.chzn-container'));
        }
    });
    $(document).on('change', '#contactiocForm input, #contactiocForm select, #contactiocForm textarea', function(e) {
        try{
            this.setCustomValidity('');
        } catch(e) {}
    });
    var validateForm = function() {
        if ($('#contactStudy option:selected').val() < 1) {
            $('#contactStudy_chzn a').addClass('input-required');
            addErrorMessage($('#contactStudy_chzn'));
            var position = $('#contactStudy_chzn').position();
            $("html, body").animate({ scrollTop: position.top - 90}, 800);
            return false;
        }
        if ($('#tipusID option:selected').val() < 1) {
            $('#tipusID_chzn a').addClass('input-required');
            addErrorMessage($('#tipusID_chzn'));
            var position = $('#tipusID_chzn').position();
            $("html, body").animate({ scrollTop: position.top - 90}, 800);
            return false;
        }
        $.each($('input[type="text"],input[type="email"]'), function (index, obj) {
            if ($.trim($(obj).val()).length == 0) {
                return false;
            }
        });
        return true;
    };
    var addErrorMessage = function($node) {
        var message = Joomla.JText._('COM_CONTACTIOC_INVALID_SELECT');
        removeErrorMessage($node.find('.clarification.error'));
        $node.append('<div class="clarification error">' + message + '</div>');
    };
    var removeErrorMessage = function($node) {
        if ($node) {
            $node.empty();
        }
    };
})(jQuery);
