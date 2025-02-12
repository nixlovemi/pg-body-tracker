// Livewire: https://laravel-livewire.com/docs/2.x/reference#global-livewire-js

(function($) {

    // BASE FUNCTIONS
    $(document).ready(function(){
        $(document).on('click', 'div.alert button.btn-close', function(e){
            $(this).parent().fadeOut(500);
        });

        $(document).on('click', 'a.show-info', function(e){
            const content = $(this).data('content');
            const title = $(this).data('title');

            showInfoAlert({
                icon: null,
                title: title,
                text: content,
            });
        });

        setTimeout(function(){
            $('div.alert.alert-dismissible').each(function() {
                $(this).find('.btn-close').click();
            });
        }, 12000);

        loadJqueryComponents();
        loadCharts();
    });

    //$(document).on('click', 'div.modal-dialog .btn-modal-close', function(e) {
    //    $(this).closest('div.modal').modal('toggle');
    //});

    function closeModal(modalObj)
    {
        modalObj.remove();
        $('div.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    }

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
            loadMaskMoney();
            // loadBootstrapSelect();
            loadDatePicker();
        }, 250);
    }

    function loadCharts()
    {
        setTimeout(function(){
            initChartClientGoal();
        }, 250);
    }

    function loadDatePicker()
    {
        // TODO: create eng version
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

    function loadMaskMoney()
    {
        $(".jq-mask-money").maskMoney({
            // prefix: the prefix to be displayed before(aha!) the value entered by the user(example: "US$ 1234.23"). default: ''
            // suffix: the prefix to be displayed after the value entered by the user(example: "1234.23 €"). default: ''
            // affixesStay: set if the prefix and suffix will stay in the field's value after the user exits the field. default: true
            // thousands: the thousands separator. default: ','
            // decimal: the decimal separator. default: '.'
            // precision: how many decimal places are allowed. default: 2
            // allowZero: use this setting to prevent users from inputing zero. default: false
            // allowNegative: use this setting to prevent users from inputing negative values. default: false
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

    // TEMPLATE FUNCTIONS
    $(document).on('click', '#btn-client-new-goal', function(e) {
        showJsonAjaxModal('GET', '/app/goal/htmlModalAdd', {
            'cuid': $(this).closest('form#client-form').find('input[name="f-cid"]').val(),
            'cedit': $(this).closest('form#client-form').find('input[name="f-cedit"]').val(),
            'json': 1
        });
    });

    $(document).on('click', 'form#client-form #btn-client-remove-goal', function(e) {
        var confirm = getConfirm({
            title: $(this).data('confirm-title'),
            text: $(this).data('confirm-text'),
        });
        confirm.fire().then((result) => {
            if (!result.isConfirmed) {
                return false;
            }

            // call ajax and remove goal
            const SPAN_QUOTE_CARD = $('form#client-form div#dv-card-client-goals');
            const FORM_ID = 'temp-client-goal-form-delete';

            $(document).find(`form#${FORM_ID}`).remove();
            var FORM = document.createElement('form');
            FORM.id = FORM_ID;

            var inputGcid = document.createElement('input');
            inputGcid.type = 'hidden';
            inputGcid.name = 'f-gcid';
            inputGcid.value = $(this).data('gcid');
            FORM.appendChild(inputGcid);

            var inputCcid = document.createElement('input');
            inputCcid.type = 'hidden';
            inputCcid.name = 'f-cid';
            inputCcid.value = $(this).data('ccid');
            FORM.appendChild(inputCcid);

            var inputCedt = document.createElement('input');
            inputCedt.type = 'hidden';
            inputCedt.name = 'f-cedit';
            inputCedt.value = $(this).data('cedt');
            FORM.appendChild(inputCedt);

            var inputCsrf = document.createElement('input');
            inputCsrf.type = 'hidden';
            inputCsrf.name = '_token';
            inputCsrf.value = $('#card-client-goal-form-row-content input[name="_token"]').val();
            FORM.appendChild(inputCsrf);
            document.body.appendChild(FORM);

            submitModalForm($(document).find(`form#${FORM_ID}`), function(retorno) {
                $(document).find(`form#${FORM_ID}`).remove();

                showSuccessAlert({
                    'title': 'Sucesso!',
                    'text': retorno.message
                });

                SPAN_QUOTE_CARD.html(retorno.data.html);
                setTimeout(function(){
                    initLivewireTable();
                    loadJqueryComponents();
                    loadCharts();
                }, 250);

            }, '/app/goal/doModalRemove');
        });
    });

    $(document).on('click', 'form#register-goal-form .btn-modal-submit', function(e) {
        const FORM = $(this).closest('form');
        const SPAN_QUOTE_CARD = $('form#client-form div#dv-card-client-goals');

        submitModalForm(FORM, function(retorno) {

            showSuccessAlert({
                'title': 'Sucesso!',
                'text': retorno.message
            });

            closeModal(FORM.closest('div.modal').parent());

            SPAN_QUOTE_CARD.html(retorno.data.html);
            setTimeout(function(){
                initLivewireTable();
                loadJqueryComponents();
                loadCharts();
            }, 250);

        }, '/app/goal/doModalAdd');
    });

    $(document).on('click', '#btn-client-past-goals', function(e) {
        showJsonAjaxModal('GET', '/app/goal/htmlModalPastGoals', {
            'cuid': $(this).closest('form#client-form').find('input[name="f-cid"]').val(),
            'json': 1
        });

        setTimeout(function(){
            initChartClientGoal('dv-modal-past-goals');
        }, 500);
    });

    $(document).on('click', '#list-client-past-goals-more a', function(e) {
        let clientCodedId = $(this).data('ccid');
        let beforeDeadline = $(this).data('bdline');
        let MAIN_DIV = $(document).find('div#list-client-past-goals-more');
        let CSRF = MAIN_DIV.find('input[name="_token"]').val();

        ajaxSetup(CSRF);
        $.ajax({
            type: 'POST',
            url: '/app/goal/htmlModalPastGoals',
            data: {
                cuid: clientCodedId,
                bdline: beforeDeadline,
                json: 1,
                fullModal: 0
            },
            beforeSend: function() {
                showLoader();
            },
            success: function (retorno) {
                if (retorno.error) {
                    showErrorAlert({
                        'title': 'Erro!',
                        'text': retorno.message
                    });
                    return;
                }

                MAIN_DIV.html(retorno.data.html).removeClass('text-center');
                setTimeout(function() {
                    initChartClientGoal(MAIN_DIV.attr('id'));

                    setTimeout(function(){
                        MAIN_DIV.attr("id", "");
                    }, 600);
                }, 300);
            },
            complete: function() {
                closeLoader();
            },
            error: function (data) {
                showErrorAlert({
                    'title': 'Erro!',
                    'text': 'Ocorreu um erro inesperado! Tente novamente.'
                });
            }
        });
    });

    $(document).on('click', '#btn-client-new-avaliation', function(e) {
        showJsonAjaxModal('GET', '/app/avaliation/htmlModalAdd', {
            'cuid': $(this).closest('form#client-form').find('input[name="f-cid"]').val(),
            'cedit': $(this).closest('form#client-form').find('input[name="f-cedit"]').val(),
            'json': 1
        });
    });

    $(document).on('click', 'form#register-avaliation-form .btn-modal-submit', function(e) {
        const FORM = $(this).closest('form');

        submitModalForm(FORM, function(retorno) {

            showSuccessAlert({
                'title': 'Sucesso!',
                'text': retorno.message
            });

            closeModal(FORM.closest('div.modal').parent());
            setTimeout(function(){
                refreshLivewireTable(`#dv-card-client-avaliations`);
            }, 250);

        });
    });

    function initChartClientGoal(mainDivId)
    {
        mainDivId = mainDivId || '';
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        // loop all canvas with class 'chart-client-goal'
        let selector = 'canvas.chart-client-goal';
        if (mainDivId != '') {
            selector = '#' + mainDivId + ' ' + selector;
        }
        $(selector).each(function() {
            const chartId = $(this).attr('id');
            initChartClientGoalSingle(chartId);
        });
    }

    function initChartClientGoalSingle(chartId)
    {
        // get data from input hidden ID with -data
        const data = JSON.parse(document.getElementById(chartId + '-data').value);

        // get canvas element
        const ctx = document.getElementById(chartId);

        // config chart
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        bottom: 20
                    }
                },
                plugins: {
                    legend: {
                        onClick: null
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            padding: 10
                        },
                        offset: true
                    },
                    y: {
                        beginAtZero: false,
                        min: 75,
                        max: 130,
                        ticks: {
                            padding: 15
                        }
                    }
                }
            }
        };

        // create chart
        const myChart = new Chart(ctx, config);
    }
    // ==================

}(jQuery));
