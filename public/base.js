// Livewire: https://laravel-livewire.com/docs/2.x/reference#global-livewire-js

(function($) {

    // BASE FUNCTIONS
    $(document).ready(function(){
        $(document).on('click', 'div.alert button.btn-close', function(e){
            $(this).parent().fadeOut(500);
        });

        setTimeout(function(){
            $('div.alert.alert-dismissible').each(function() {
                $(this).find('.btn-close').click();
            });
        }, 12000);

        loadJqueryComponents();
    });

    $(document).on('click', 'div.modal-dialog .btn-modal-close', function(e) {
        $(this).closest('div.modal').modal('toggle');
    });

    function showLoader()
    {
        $.LoadingOverlay("show");
        setTimeout(function(){
            $.LoadingOverlay("hide");
        }, 10000);
    }

    function closeLoader()
    {
        $.LoadingOverlay("hide");
    }

    function ajaxSetup(csrf)
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrf ?? $('meta[name="csrf-token"]').attr('content'),
                // 'Authorization': `Bearer ${USER_API_TOKEN_ID}`,
                // 'domain': DOMAIN_CODED
            }
        });
    }

    function uuidv4() {
        return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
          (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }

    function getAjaxErrorMsg(data)
    {
        if (typeof data.responseJSON == 'undefined' || typeof data.responseJSON.message == 'undefined') {
            return 'Erro ao processar essa requisição!';
        }

        return data.responseJSON.message;
    }

    function loadJqueryComponents()
    {
        setTimeout(function(){
            // loadMaskMoney();
            // loadBootstrapSelect();
            loadDatePicker();
        }, 250);
    }

    function loadDatePicker()
    {
        $(".jq-datepicker").datepicker({
            dateFormat: 'dd/mm/yy',
            closeText:"Fechar",
            prevText:"&#x3C;Anterior",
            nextText:"Próximo&#x3E;",
            currentText:"Hoje",
            monthNames: ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
            monthNamesShort:["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
                dayNames:["Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado"],
                dayNamesShort:["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
            dayNamesMin:["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
            weekHeader:"Sm",
            firstDay:1
        });
    }

    /*
        function loadMaskMoney()
        {
            $(".jq-mask-money").maskMoney({
                // prefix:'R$ ',
                allowNegative: true,
                thousands: '.',
                decimal: ',',
                // affixesStay: false
            });
        }

        function loadBootstrapSelect()
        {
            $('.bootstrap-select').selectpicker({
                style: '',
                styleBase: 'form-select'
            });
        }
    */

    function showBootstrapModal(html)
    {
        $('div[id^="bootstrap-modal-"]').remove();
        const eventDivId = 'bootstrap-modal-' + uuidv4();
        $('body').append(`<div id="${eventDivId}">${html}</div>`);
        const jqObj = $('#' + eventDivId).find('div.modal');

        var myModal = new bootstrap.Modal(document.getElementById(jqObj[0].id));
        myModal.show();

        return myModal;
    }

    function enableFormWhileSaving(formObj)
    {
        formObj.find(":input").prop("disabled", false);
    }

    function disableFormWhileSaving(formObj)
    {
        formObj.find(":input").prop("disabled", true);
    }

    function showJsonAjaxModal(type, url, data, csrf=null)
    {
        ajaxSetup(csrf);

        $.ajax({
            type,
            url,
            data,
            dataType: 'json',
            beforeSend: function(){showLoader()},
            success: function (retorno) {
                if (retorno.error) {
                    showErrorAlert({
                        title: 'Erro',
                        text: retorno.message
                    });
                    return;
                }

                showBootstrapModal(retorno.data.html);
                loadJqueryComponents();
            },
            complete: function(){closeLoader()},
            error: function (data) {
                showErrorAlert({
                    title: 'Erro',
                    text: getAjaxErrorMsg(data)
                });
            }
        });
    }

    function submitModalForm(oForm, successFnc, actionUrl=null, customData={}, skipDisableForm=false)
    {
        let FORM = oForm;
        let CSRF = FORM.find('input[name="_token"]').val();

        ajaxSetup(CSRF);
        let formData = new FormData(FORM[0]);
        for (const [key, value] of Object.entries(customData)) {
            formData.append(key, value);
        }

        $.ajax({
            type: 'POST',
            url: actionUrl ?? FORM.attr('action'),
            data: formData,
            dataType: 'json',
            processData: false, // required for FormData with jQuery
            contentType: false, // required for FormData with jQuery
            beforeSend: function() {
                showLoader();
                if (!skipDisableForm) {
                    disableFormWhileSaving(FORM);
                }
            },
            success: function (retorno) {
                if (retorno.error) {
                    showErrorAlert({
                        'title': 'Erro!',
                        'text': retorno.message
                    });
                    return;
                }

                successFnc(retorno);
            },
            complete: function() {
                closeLoader();
                if (!skipDisableForm) {
                    enableFormWhileSaving(FORM);
                }
            },
            error: function (data) {
                showErrorAlert({
                    'title': 'Erro!',
                    'text': 'Ocorreu um erro inesperado! Tente novamente.'
                });
                if (!skipDisableForm) {
                    enableFormWhileSaving(FORM);
                }
            }
        });
    }
    // ==============

    // THEME
    $(document).on('submit', 'form#frm-filter-attendance', function(e) {
        e.preventDefault();
        let FORM = $(this);
        let CSRF = FORM.find('input[name="_token"]').val();

        ajaxSetup(CSRF);
        let formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: FORM.attr('action'),
            data: formData,
            dataType: 'json',
            processData: false, // required for FormData with jQuery
            contentType: false, // required for FormData with jQuery
            beforeSend: function() {
                showLoader();
                disableFormWhileSaving(FORM);
                $('#attendance-list-table').html('Carregando ...');
            },
            success: function (retorno) {
                if (retorno.error) {
                    showErrorAlert({
                        'title': 'Erro!',
                        'text': retorno.message
                    });
                    return;
                }

                $('#attendance-list-table').html(retorno.data.html);
                setTimeout(function(){
                    loadJqueryComponents();
                    initLivewireTable();
                }, 250);
            },
            complete: function() {
                closeLoader();
                enableFormWhileSaving(FORM);
            },
            error: function (data) {
                showErrorAlert({
                    'title': 'Erro!',
                    'text': 'Ocorreu um erro inesperado! Tente novamente.'
                });
                enableFormWhileSaving(FORM)
            }
        });
    });
    // =====

    // sweet alert
    /**
     *
     * @param {*} objVar [title|text]
     */
    function showAlert(typeStr, objVar)
    {
        Swal.fire({
            icon: typeStr,
            title: objVar.title,
            html: objVar.text,
            // footer: '<a href="">Why do I have this issue?</a>'
        });
    }

    function showErrorAlert(objVar)
    {
        showAlert('error', objVar);
    }

    function showSuccessAlert(objVar)
    {
        showAlert('success', objVar);
    }

    function showWarningAlert(objVar)
    {
        showAlert('warning', objVar);
    }

    function showInfoAlert(objVar)
    {
        showAlert('info', objVar);
    }

    function getConfirm(objVar)
    {
        return Swal.mixin({
            title: objVar.title,
            html: objVar.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim!',
            cancelButtonText: "Fechar",
        });
    }
    // ===========

    // Livewire
    function initLivewireTable()
    {
        Livewire.start();
    }

    function refreshLivewireTable(parentSelector)
    {
        var id = $(`${parentSelector} div[wire\\:id]`).attr('wire:id');
        var Liv = Livewire.find(id);
        Liv.refresh();
    }

    function refreshAllLivewireTables()
    {
        $(`div[wire\\:id]`).each(function() {
            var id = $(this).attr('wire:id');
            var Liv = Livewire.find(id);
            Liv.refresh();

            delete Liv;
        });
    }

    Livewire.on('laraveltable:link:open:newtab', (url) => {
        window.open(url, '_blank').focus();
    });

    Livewire.on('laraveltable:action:feedback', (feedbackMessage) => {
        // Replace this native JS alert by your favorite modal/alert/toast library implementation. Or keep it this way!
        // window.alert(feedbackMessage);

        showInfoAlert({
            icon: null,
            title: 'Informação',
            text: feedbackMessage,
        });
    });

    Livewire.on('laraveltable:action:confirm', (actionType, actionIdentifier, modelPrimary, confirmationQuestion) => {
        // You can replace this native JS confirm dialog by your favorite modal/alert/toast library implementation. Or keep it this way!
        /*
        if (window.confirm(confirmationQuestion)) {
            // As explained above, just send back the 3 first argument from the `table:action:confirm` event when the action is confirmed
            Livewire.emit('laraveltable:action:confirmed', actionType, actionIdentifier, modelPrimary);
        }
        */

        var confirm = getConfirm({
            title: 'Confirmação',
            text: confirmationQuestion
        });
        confirm.fire().then((result) => {
            if (!result.isConfirmed) {
                return false;
            }

            Livewire.emit('laraveltable:action:confirmed', actionType, actionIdentifier, modelPrimary);
        });
    });

    Livewire.on('laraveltable:link:open:modal', (url, urlParam) => {
        // window.open(url, '_blank').focus();

        const emptyParam = (JSON.stringify(urlParam) === '{}') || (JSON.stringify(urlParam) === '"[]"' || (JSON.stringify(urlParam) === '[]'));
        showJsonAjaxModal('GET', url, emptyParam ? null: urlParam);
    });

}(jQuery));
