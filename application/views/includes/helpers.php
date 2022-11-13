<script>
//Only when the document has fully loaded
    var idleTime = 0;
    var periods = ['Day(s)','Week(s)','Month(s)','Year(s)'];
    var start_date, end_date;
    $(document).ready(function () {
        //for all forms with the formValidate class, validate the form and send data to the database
        //$('form.formValidate').validator().on('submit', saveData);
        //Any edit to be done uses this code for modal popu-up
        $('table tbody').on('click', 'tr .edit_me', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var formId = tbl_id.replace("tbl", "form");

            console.log(formId);
            //these are the specific table ids from which we identify which forms can be populated by the set selects function
            var applicable_tables = ["tblAddress","tblSaving_fees","tblLoan_fee","tblFixed_asset", "tblJournal_transaction","tblExpense", "tblBill", "tblIncome", "tblInvoice"];
            if (check_in_array(tbl_id, applicable_tables) && typeof set_selects !== 'undefined' && typeof set_selects === "function") {
                set_selects(data, formId);
            } else {
                edit_data(data, formId);
            }

        });

        //duplicated the functionality up in order to create a work around when a single table is having to two popup modals per row
         $('table tbody').on('click', 'tr .edit_me2', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var tbl_id2 = $(tbl).attr("data-id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var formId = tbl_id2.replace("tbl", "form");
            //these are the specific table ids from which we identify which forms can be populated by the set selects function
            var applicable_tables = ["tblAddress","tblSaving_fees","tblLoan_fee","tblFixed_asset", "tblJournal_transaction","tblExpense", "tblBill", "tblIncome", "tblInvoice","Portfolio_aging"];
            if (check_in_array(tbl_id, applicable_tables) && typeof set_selects !== 'undefined' && typeof set_selects === "function") {
                set_selects(data, formId);
            } else {
                edit_data(data, formId);
            }

        });

        //This code section to change the status of a given record with the change status class
        $('table tbody').on('click', 'tr .change_status', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var url = $(this).attr("data-href");
            if (typeof url === 'undefined') {
                var controller = tbl_id.replace("tbl", "");
                var url = "<?php echo site_url(); ?>/" + controller.toLowerCase() + "/change_status";
            }

            change_status({id: data.id, status_id: (parseInt(data.status_id) === 1 ? 2: 1)}, url, tbl_id);
        });
         $('table tbody').on('click', 'tr .unblock_account', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var url = $(this).attr("data-href");
            if (typeof url === 'undefined') {
                var controller = tbl_id.replace("tbl", "");
                var url = "<?php echo site_url(); ?>/" + controller.toLowerCase() + "/unblock_account";
            }

            unblock_account({id: data.user_id}, url, tbl_id);
        });

        
        //Any delete to be done uses this code for modal pop-up
        $('table tbody').on('click', 'tr .delete_me', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/delete";
            delete_item("Are you sure, you want to delete this record?", data.id, url, tbl_id);
        });
        //Increment the idle time counter every minute.
        setInterval(timerIncrement, 60000); // 1 minute
        //Zero the idle timer on mouse movement.
        $(this).mousemove(function (e) {
            if (!$('#myLockscreen').is(':visible')) {
                idleTime = 0;
            }
        });

    
        //Zero the idle timer on mouse movement and on key press.
        $(this).keypress(function (e) {
            if (!$('#myLockscreen').is(':visible')) {
                idleTime = 0;
            }
        });
        // clear the fields in forms whose modal dialogs have been closed
        $("div.modal").on("hide.bs.modal", function () {
            var steps_form1=$('#add_client_loan-modal');
            var steps_form2=$('#top_client_loan-modal');
            if (steps_form1.length > 0) {
                steps_form1.steps("reset");
            }
            if (steps_form2.length > 0) {
                steps_form2.steps("reset");
            }
            var forms = $('form', this);
            clear_forms(forms);
        });

        if ($('.date').length > 0) {
            $('.date').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: "dd-mm-yyyy"
            }).on('hide', function (e) {
                e.stopPropagation();
            }).on('changeDate', function (e) {
                $(e.target).trigger('change');
            });
        }


        //For the search member popup
        $('#modalSearch').on('shown.bs.modal', function() {
        $('#top-search').focus();
        });

    });//End of the document is ready operations
    //function to clear the forms
    function clear_forms(forms) {
        if (forms.length) {
            //lets go thru each and every form element which is not a form submit element and clear the value
            var form_elements = $('input:not(:submit)input:not(:hidden),select,textarea', $(forms[0]));
            $.each(form_elements, function (key, form_element) {
                if (form_element.type === 'radio' || form_element.type === 'checkbox') {
                    $(form_element).prop("checked", false).trigger('change');
                    if(typeof client_loanModel !=='undefined' && typeof client_loanModel.checkbox!=='undefined'){
                        client_loanModel.checkbox(false);
                    }
                }else{
                    $(form_element).val('').trigger('change');
                }
            });
            forms[0].reset();
            $('input[name="id"]', $(forms[0])).val('');
            $('input[name="payment_date"]', this).val('');
            
            var datepicker = $('.date', $(forms[0]));
            if (datepicker.length) {
                datepicker.datepicker('clearDates');
            }
        }
    }
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "preventDuplicates": false,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    //set up the knockout bindings
    if (typeof ko !== 'undefined') {

        ko.bindingHandlers.select2 = {
            init: function (el, valueAccessor, allBindingsAccessor, viewModel) {
                ko.utils.domNodeDisposal.addDisposeCallback(el, function () {
                    $(el).select2('destroy');
                });

                var allBindings = allBindingsAccessor(),
                        select2 = ko.utils.unwrapObservable(allBindings.select2);
                $(el).select2(select2);
            },
            update: function (el, valueAccessor, allBindingsAccessor, viewModel) {
                var allBindings = allBindingsAccessor();

                if ("value" in allBindings) {
                    if ((allBindings.select2.multiple || el.multiple) && allBindings.value().constructor !== Array) {
                        $(el).val(allBindings.value().split(',')).trigger('change');
                    } else {
                        $(el).val(allBindings.value()).trigger('change');
                    }
                } else if ("selectedOptions" in allBindings) {
                    var converted = [];
                    var textAccessor = function (value) {
                        return value;
                    };
                    if ("optionsText" in allBindings) {
                        textAccessor = function (value) {
                            var valueAccessor = function (item) {
                                return item;
                            };
                            if ("optionsValue" in allBindings) {
                                valueAccessor = function (item) {
                                    return item[allBindings.optionsValue];
                                };
                            }
                            var items = $.grep(allBindings.options(), function (e) {
                                return valueAccessor(e) === value;
                            });
                            if (items.length === 0 || items.length > 1) {
                                return "UNKNOWN";
                            }
                            return items[0][allBindings.optionsText];
                        };
                    }
                    $.each(allBindings.selectedOptions(), function (key, value) {
                        converted.push({id: value, text: textAccessor(value)});
                    });
                    $(el).select2("data", converted);
                }
                $(el).trigger("change");
            }
        };

        ko.bindingHandlers.datepicker = {
            init: function (element, valueAccessor, allBindingsAccessor) {
                //initialize datepicker with some optional options
                var options = allBindingsAccessor().datepickerOptions || {
                    keyboardNavigation: false,
                    forceParse: false,
                    calendarWeeks: true,
                    autoclose: true,
                    format: 'dd-mm-yyyy'
                };
                $(element).datepicker(options).on('hide', function (e) {
                    e.stopPropagation();
                }).on('changeDate', function (e) {
                    $(e.target).trigger('change');
                });

                //when a user changes the date, update the view model
                ko.utils.registerEventHandler(element, "changeDate", function (event) {
                    var value = valueAccessor();
                    if (ko.isObservable(value)) {
                        value(moment(event.date).format('DD-MM-YYYY'));
                    }
                });
            },
            update: function (element, valueAccessor) {
                var value = ko.utils.unwrapObservable(valueAccessor());
                $(element).val(value);
            }
        };
    }
    //return Initials of all the words in a string
    function abbreviate(str) {                     //pull out initials
        var matches = str.match(/\b(\w)/g);
        var acronym = matches.join('').toUpperCase();
        return(acronym);
    }
    //return a number with zeros prepended to it
    function zeroFill(number, width) {
        //Prrepend with zeros
        width -= number.toString().length;
        if (width > 0)
        {
            return new Array(width + (/\./.test(number) ? 2 : 1)).join('0') + number;
        }
        return number + ""; // always return a string
    }
    function disburseLoan(form){
        event.preventDefault();
        var $form = $(form);
        var formData = new FormData($form[0]);
        Swal.fire({
          title: 'Payment Mode',
          html: "<h2 style='color:blue;font-weight:bold;'>"+formData.get('payment_mode')+"</h2><br><br>The loan will be disbursed via <b>"+formData.get('payment_mode')+" !",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor:"#0eb337",
          confirmButtonText:'Yes, Continue!',
          cancelButtonText: 'No, Cancel!',
          reverseButtons: false
        }).then((result) => {
          if (result.isConfirmed) {
           if(formData.get('preferred_payment_id')==4){
            disburse_money(formData)
           }else{
           saveData2(form)
           }
          } else  {
            
          }
        })

    }

     function disburse_money(formData) {
        $url='<?php echo base_url("u/SentePay/disburseLoan");?>';
        Swal.fire({
          title: '<strong style="color:#1c87c9;">Payment Request</strong>',
          icon:'success',
          html:
            'Please confirm the details below<br><br>'+
            '<table style="width:100%;text-align:left;"><tr style="border: 01px solid #dddddd;padding: 8px;"><th>Name</th><td><span style="font-size:20px;">| <b>'+formData.get("member_name")+'</b></span></td></tr><tr style="border: 1px solid #dddddd;padding: 8px;"><th>Phone No</th><td><span style="font-size:20px;">| <b>'+formData.get("phone_number")+'</b></span></td>'+
             '<tr style="border: 1px solid #dddddd;padding: 8px;"><th>Amount</th><td><span style="font-size:20px;">| <b>'+curr_format(round(formData.get("amount_approved"),0))+'</b></span></td></tr></table>',
          //footer: '<a href="<?php //echo base_url("member/member_personal_info/1");?>" target="_blank">Check Biodata</a>',
          focusConfirm: false,
          showCancelButton: true,
          confirmButtonColor:"#0eb337",
          cancelButtonColor:"#FF4500",
          confirmButtonText: 'Process Payment',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true,
          allowOutsideClick:false,
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            return $.ajax({
                    url: $url,
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    
                    }).then(response => {
                        if (!response.success) {
                        throw new Error(response.message)
                        }
                        return response
                    })
                  .catch(error => {
                            Swal.showValidationMessage(
                            `Request failed: ${error}`
                            )
                        })
         },
                     // allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
                      if (result.isConfirmed) {
                        Swal.fire({
                          title: result.value.message,
                          icon: 'success',
                          focusConfirm: false,
                          confirmButtonColor:"#0eb337",
                          confirmButtonText: 'Done!',
                          showConfirmButton: true,
                        });
                        // start timer to clear pending sentepay payouts
                        ( () => {

                            let count = 1000;
                            let callback = (timer) => {
                                clear_pending_sentepay_payouts().then(res => {
                                    if(res.success) return clearTimeout(timer);
                                    count = count * 5;
                                    let timerId = setTimeout(() => callback(timerId), count);
                                }).catch(err => {
                                    count = count * 5;
                                    let timerId = setTimeout(() => callback(timerId), count);
                                });
                            }; 

                            let timerId = setTimeout(() => callback(timerId), count);

                        })();
                        
                      }else{
                        Swal.fire({
                          title: "Notice!",
                          html:"No worries, you can process this payment later from <b>Payout Requests</b> report",
                          icon: 'info',
                          focusConfirm: false,
                          confirmButtonColor:"#0eb337",
                          confirmButtonText: 'Ok!',
                          showConfirmButton: true,
                        })
                      }
          })
    }//End of the success

    const clear_pending_sentepay_payouts = () => {
        $url = '<?php echo base_url("System_automations/clear_pending_sente_pay_payouts"); ?>';

        return new Promise((resolve, reject) => {
            $.ajax({
                url: $url,
                type: 'POST',
                dataType: 'json',
            }).then(response => {
                if (!response.success) {
                    // Notify User
                    //throw new Error(response.message)
                }
                resolve(response);
            })
            .catch(error => {
                // Handle Error
                reject({success: false});
            })
        });
    }

    function resend_money(data) {
        $url='<?php echo base_url("u/SentePay/disburseLoan");?>';
        Swal.fire({
          title: '<strong style="color:#1c87c9;">Process Payment?</strong>',
          icon:'question',
          html:
            'Please confirm the details below<br><br>'+
            '<table style="width:100%;text-align:left;"><tr style="border: 01px solid #dddddd;padding: 8px;"><th>Name</th><td><span style="font-size:20px;">| <b>'+data.member_name+'</b></span></td></tr><tr style="border: 1px solid #dddddd;padding: 8px;"><th>Phone No</th><td><span style="font-size:20px;">| <b>'+data.phone_number+'</b></span></td>'+
             '<tr style="border: 1px solid #dddddd;padding: 8px;"><th>Amount</th><td><span style="font-size:20px;">| <b>'+curr_format(round(data.amount_approved,0))+'</b></span></td></tr></table>',
          //footer: '<a href="<?php //echo base_url("member/member_personal_info/1");?>" target="_blank">Check Biodata</a>',
          focusConfirm: false,
          showCancelButton: true,
          confirmButtonColor:"#0eb337",
          cancelButtonColor:"#FF4500",
          confirmButtonText: 'Yes, Send!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true,
          allowOutsideClick:false,
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            return $.ajax({
                    url: $url,
                    type: 'POST',
                    dataType: 'json',
                    data: {client_loan_id: data.id,member_id:data.member_id,amount_approved:data.amount_approved,phone_number:data.phone_number},
                  
                    }).then(response => {
                    if (!response.success) {
                      throw new Error(response.message)
                    }
                    return response
                  })
                  .catch(error => {
                    Swal.showValidationMessage(
                      `Request failed: ${error}`
                    )
                  })
                    },
                     // allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                      if (result.isConfirmed) {
                        Swal.fire({
                          title: result.value.message,
                          icon: 'success',
                          focusConfirm: false,
                          confirmButtonColor:"#0eb337",
                          confirmButtonText: 'Done!',
                          showConfirmButton: true,
                        })
                      }
                    })
    }//End of the success
//This function helps to add and update info
    function saveData(e) {
        if (e.isDefaultPrevented()) {
            // handle the invalid form...
            console.log('Please fill all the fields correctly');
        } else {
            // everything looks good!
            e.preventDefault();
            saveData2(e.target);
        }
    }//End of the saveData function
    function saveData2(form) {
        var $form = $(form);//fv = $form.data('formValidation'),
        var formData = new FormData($form[0]);
        var id = $form.attr('id');
        var url = $form.attr('action');

       Swal.fire({
          title: 'Are You Sure You want to proceed?',
          icon:'warning',
          html:
            '',
          focusConfirm: false,
          showCancelButton: true,
          confirmButtonColor:"#0eb337",
          cancelButtonColor:"#FF4500",
          confirmButtonText: 'Yes, Proceed',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true,
          allowOutsideClick:false,
          showLoaderOnConfirm: true,
       }).then((result) => {
          if (result.isConfirmed) {
            $("body").addClass("loading"); 

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function () {
                    enableDisableButton(form, true);
                },
                success: function (feedback) {
                    if (feedback.success) {
                        if (isNaN(parseInt($form.attr('id')))) {
                            $modal = $form.parents('div.modal');
                            if ($modal.length) {
                                $form[0].reset();
                                $($modal[0]).modal('hide');
                            }
                        }
                        setTimeout(function () {
                            var formId = $form.attr('id');
                            var tblId = formId.replace("form", "tbl");
                            if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                            }
                            if (typeof reload_data === "function") {
                                reload_data(formId, feedback);
                            }
                        }, 1000);
                        $("body").removeClass("loading"); 
                        toastr.success(feedback.message, "Success");
                    } else {
                        $("body").removeClass("loading");
                        toastr.warning(feedback.message, "Failure!");
                    }
                    //enableDisableButton(form, false);
                }, error: function (jqXHR, textStatus, errorThrown) {
                    $("body").removeClass("loading");
                    network_error(jqXHR, textStatus, errorThrown, form);
                },
                complete: function () {
                    enableDisableButton(form, false);
                }
            });
           
          } else  {
            $("body").removeClass("loading");
          }
        });

    }//End of the saveData2 function

      function saveData10(form) {
        var $form = $(form);//fv = $form.data('formValidation'),
        var formData = new FormData($form[0]);
        var id = $form.attr('id');
        var table = $form.attr('data-table-id');
       $("body").addClass("loading"); 
       
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function () {
                enableDisableButton(form, true);
            },
            success: function (feedback) {
                if (feedback.success) {
                    if (isNaN(parseInt($form.attr('id')))) {
                        $modal = $form.parents('div.modal');
                        if ($modal.length) {
                            $form[0].reset();
                            $($modal[0]).modal('hide');
                        }
                    }
                    setTimeout(function () {
                        var formId = $form.attr('id');
                        // var tblId = formId.replace("form", "tbl");
                        var tblId = table;
                        console.log("Hello", table);
                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                        }
                        if (typeof reload_data === "function") {
                            reload_data(formId, feedback);
                        }
                    }, 1000);
                     $("body").removeClass("loading"); 
                    toastr.success(feedback.message, "Success");
                } else {
                    $("body").removeClass("loading");
                    toastr.warning(feedback.message, "Failure!");
                }
                //enableDisableButton(form, false);
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("body").removeClass("loading");
                network_error(jqXHR, textStatus, errorThrown, form);
            },
            complete: function () {
                enableDisableButton(form, false);
            }
        });
    }//End of the saveData2 function


    function saveData9(form,action) {
        var $form = $(form);//fv = $form.data('formValidation'),
        var formData = new FormData($form[0]);
        var id = $form.attr('id');
        formData.set('state_id', 20);
        $("body").addClass("loading"); 
        //console.log("SAVEDATA9");
        //console.log($form.attr('action'));
        

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (feedback) {
                if (feedback.success) {
                    if (isNaN(parseInt($form.attr('id')))) {
                        $modal = $form.parents('div.modal');
                        if ($modal.length) {
                            $form[0].reset();
                            $($modal[0]).modal('hide');
                            //if(parseInt(action)==1){
                            disburse_money(formData);
                            //}
                        }
                    }
                    setTimeout(function () {
                        var formId = $form.attr('id');
                        var tblId = formId.replace("form", "tbl");
                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                        }
                        if (typeof reload_data === "function") {
                            reload_data(formId, feedback);
                        }
                    }, 1000);
                     $("body").removeClass("loading"); 
                    toastr.success(feedback.message, "Success");
                } else {
                    $("body").removeClass("loading");

                    toastr.warning(feedback.message, "Failure!");
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("body").removeClass("loading");
                network_error(jqXHR, textStatus, errorThrown, form);
            },
            complete: function () {
                enableDisableButton(form, false);
            }
        });
    }//End of the saveData9 function




    function openModal() {
      
      $(".openBtn").on("click", function (e) {
         
        e.stopImmediatePropagation();
        var dataURL = $(this).attr("data-href");
        console.log(dataURL);
        var provisionAmount = $(this).attr("data-required_provision_amount");
        $("#loan_loss_provision-modal").modal("show").find(".modal-content").load(dataURL);
      });
    }
    openModal();
    
    //This functi;on helps to pass data for edit to the modal form
    function edit_data(data_array, form) {
        $.each(data_array, function (key, val) {
            $.map($('#' + form + ' [name="' + key + '"]'), function (named_item) {
                if(named_item.type === 'file'){
                    //we'll figure out what to do with file input types
                }
                else if (named_item.type === 'radio' || named_item.type === 'checkbox') {
                    $(named_item).prop("checked", (named_item.value == val ? true : false)).trigger('change');
                } else {
                    $(named_item).val(val).trigger('change');
                    if(key=='account_code'){
                        const account_code_array = val.toString().split("-");
                        account_code_array.length && $("input[name='parent_account_id']").val(data_array['parent_account_id']).trigger('change');
                        account_code_array.length && $("input[name='new_account_code']").val(account_code_array[account_code_array.length-1]).trigger('change');
                    }
                    var reg_ex = /date/;
                    if (reg_ex.test(key)) {
                        if (val != null) {
                            var date_val = moment(val, 'YYYY-MM-DD').format('DD-MM-YYYY');
                            $(named_item).val(date_val).trigger('change');
                            setDpDate("#" + key, date_val);
                        }
                    }
                    var date_picker = $("#" + key).parent(".date");
                    if (date_picker.length) {
                        date_picker.datepicker('setDate', val);
                    }
                }
            });
        });
    }
    //check if an element is in array
    function check_in_array($find, $array, search_key) {
        var arr_len = $array.length;
        for (var i = 0; i < arr_len; i++) {
            var $element = $array[i];
            if (($find == ((typeof search_key === 'undefined') ? $element : $element[search_key]))) {
                return true;
            }
        }
        return false;
    }
    //set the Date picker date
    function setDpDate(field, val) {
        var date_picker = $(field).parent(".date");
        if (date_picker.length) {
            date_picker.datepicker('setDate', val);
        } else {
            date_picker = $((field + ".datepicker"));
            if (date_picker.length) {
                date_picker.datepicker('setDate', val);
            }
        }
    }
    
    //function sets the object for the buttons to be added to the datatables
    function getBtnConfig(title) {
        var btn_configs = [

            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-copy"></i>',
                titleAttr: 'Copy',
                //title: $('.download_label').html(),
                title: title,
                exportOptions: {
                    columns: ':visible'
                }
            },

            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o text-green"></i>',
                titleAttr: 'Excel',

                title: title,
                exportOptions: {
                    columns: ':visible'
                }
            },

            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-o text-green"></i>',
                titleAttr: 'CSV',
                title: title,
                exportOptions: {
                    columns: ':visible'
                }
            },

            // {
            //     extend: 'pdfHtml5',
            //     text: '<i class="fa fa-file-pdf-o text-danger"></i>',
            //     titleAttr: 'PDF',
            //     title: title,
            //     exportOptions: {
            //         columns: ':visible'

            //     }
            // }, /**/

            {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Print',
                title: title,
                customize: function (win) {
                    $(win.document.body)
                            .css('font-size', '10pt');

                    $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                },
                exportOptions: {
                    columns: ':visible'
                }
            }/*,
             
             {
             extend: 'colvis',
             text: '<i class="fa fa-columns"></i>',
             titleAttr: 'Columns',
             title: title,
             postfixButtons: ['colvisRestore']
             }*/
        ];
        return btn_configs;
    }
//function for deleting message
    function confirm_delete(msg) {
        var really = confirm(msg + "?");
        return really;
    }

//Deactivate function
    function change_status(data, url, tblId,msg=false) {
     // tring to pick delete log
     
        msg1 = "You are about to " + (data.status_id === 1 ? "" : "de") + "activate the selected record. Are you sure you would like to proceed?";
        swal.fire({
            title: "Are you sure?",
            text: msg?msg:msg1,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor:"#0eb337",
            cancelButtonColor:"#FF4500",
            confirmButtonText: "Yes, proceed",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false
        }).then(result => {
             if (result.value) {
                    $.post(
                            url,
                            data,
                            function (response) {
                                if (response.success) {
                                    setTimeout(function () {
                                        toastr.success(response.message, "Success!");
                                        //any other tasks(function) to be run here
                                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                                        }
                                        if (typeof reload_data === "function") {
                                            reload_data(tblId.replace("tbl", "form"), response);
                                        }
                                    }, 1000);
                                } else {
                                    toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Request failed!");
                                }
                            },
                            'json').fail(function (jqXHR, textStatus, errorThrown) {
                        network_error(jqXHR, textStatus, errorThrown, $("#myform"));
                    });
            }
        });
    }//End of the deleting function
//Delete function

//Deactivate function
    function unblock_account(data, url, tblId,msg=false) {
        msg1 = "You are about unblock the selected account. Are you sure you would like to proceed?";
        swal.fire({
            title: "Are you sure?",
            text: msg?msg:msg1,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#FF4500",
            confirmButtonText: "Unblock!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false
        }).then(result => {
             if (result.value) {
                    $.post(
                            url,
                            data,
                            function (response) {
                                if (response.success) {
                                    setTimeout(function () {
                                        toastr.success(response.message, "Success!");
                                        //any other tasks(function) to be run here
                                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                                        }
                                        if (typeof reload_data === "function") {
                                            reload_data(tblId.replace("tbl", "form"), response);
                                        }
                                    }, 1000);
                                } else {
                                    toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Request failed!");
                                }
                            },
                            'json').fail(function (jqXHR, textStatus, errorThrown) {
                        network_error(jqXHR, textStatus, errorThrown, $("#myform"));
                    });
                  }
                });
    }//End of the deleting 
    function delete_item(msg, id, url, tblId) {
        swal.fire({
            title: "Are you sure?",
            text: msg,
            icon: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false
        }).then(result => {
             if (result.value) {
                    $.post(
                            url,
                            {id: id},
                            function (response) {
                                if (response.success) {
                                    setTimeout(function () {
                                        toastr.success(response.message, "Success!");

                                        //any other tasks(function) to be run here
                                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                                        }
                                        if (typeof reload_data === "function") {
                                            reload_data(tblId.replace("tbl", "form"), response);
                                        }
                                    }, 1000);
                                } else {
                                    toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Deletion failure!");
                                }
                            },
                            'json').fail(function (jqXHR, textStatus, errorThrown) {
                        network_error(jqXHR, textStatus, errorThrown, $("#myform"));
                    });
                   }
                });
    }//End of the deleting function
   
//function to format currencies
    function curr_format(n) {
        var formatted = "";
        formatted = (n < 0) ? ("(" + numberWithCommas(n * -1) + ")") : numberWithCommas(n);
        return formatted;
    }
//function to remove commas
   function remove_commas(amount){
     //return=amount.replace(/,/g,'');
         
      }
      
     
//}
        
  // }

//function for rounding off currecies
    function round(value, decimals = 0) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }

//The function for making currency have commas
    function numberWithCommas(x) {
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

// Sums up values in given keys of a multidimensional array
    function sumUp(array_items, sum_key) {
        var total = 0;
        if (array_items.length) {
            $.each(array_items, function (key, array_item) {
                total += parseFloat(array_item[ sum_key ]);
            });
        }
        return total;
    }

//register the summing function for dataTables
    jQuery.fn.dataTable.Api.register('sum()', function ( ) {
        return this.flatten().reduce(function (a, b) {
            if (typeof a === 'string') {
                a = a.replace(/[^\d.-]/g, '') * 1;
            }
            if (typeof b === 'string') {
                b = b.replace(/[^\d.-]/g, '') * 1;
            }
            return a + b;
        }, 0);
    });

//send form data
    $.fn.serializeObject = function (){
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

//set options value afterwards
    setOptionValue = function (propId) {
        return function (option, item) {
            if (item === undefined) {
                option.value = "";
            } else {
                option.value = item[propId];
            }
        };
    };
//return concatenated names of the Member
    function formatMemberDetails(data) {
        if (data.loading) {
            return "No results";
        }
        //return data.salutation + " " + data.firstname + " " + data.lastname + " " + ((data.othernames) ? data.othernames : "") + " - " + data.client_no;
        return formatMember(data);
    }

    function formatMember(member) {
        if (member.id) {
            return member.salutation + " " + member.firstname + " " + member.lastname + " " + ((member.othernames) ? member.othernames : "") + " - " + member.client_no;
        }
        var baseUrl = "--select/search--";
        return baseUrl;/**/
    }
      function formatShareDetails(data) {
        if (data.loading) {
            return "No results";
        }
        //return data.salutation + " " + data.firstname + " " + data.lastname + " " + ((data.othernames) ? data.othernames : "") + " - " + data.client_no;
        return formatShareaccount(data);
    }
    function formatShareaccount(share) {
        if (share.id) {
            return share.salutation + " " + share.firstname + " " + share.lastname + " " + ((share.othernames) ? share.othernames : "") + " - " + share.share_account_no;
        }
        var baseUrl = "--select/search--";
        return baseUrl;/**/
    }


    function network_error(jqXHR, textStatus, errorThrown, formElement) {
        var msg = "Network error. Please check your network/internet connection or get in touch with the admin.";
        status = jqXHR.status;
        switch (status) {
            case 500:
                msg = "There was a server problem.\nPlease report the following message to admin\n" + textStatus;
                break;
            case 404:
                msg = "The operation was unsuccessful.\n Please report the following message to admin\n" + textStatus + "\n" + errorThrown;
                break;
            default:
                break;
        }
        toastr.error(msg, "Network failure!");
        console.log("Status : " + textStatus + "\nStatus code: " + status + "\nResponse: " + errorThrown);
        enableDisableButton(formElement, false);
    }
    var select2options = function (dropdown_parent_id = '#add_group_member-modal') {
        return {
            placeholder: "--select--",
            id: function (member) {
                return member.id;
            },
            ajax: {
                url: '<?php echo site_url("member/jsonList"); ?>',
                dataType: 'json',
                type: 'post',
                data: function (params) {
                    var query = {
                        term: params.term,
                        status_id: 1,
                        no_accounts: 1,
                        page: params.page || 1
                    };
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                delay: 250, // wait 250 milliseconds before triggering the request
                processResults: function (result) {
                    /*result.data.forEach(function (entry, index) {
                     console.log(typeof entry.id);
                     entry.id = '' + entry.id; // Better if you can assign a unique value for every entry, something like UUID
                     });*/
                    // Tranforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: result.data
                    };
                }
            },
            dropdownParent: $(dropdown_parent_id),
            //minimumInputLength: 3,
            minimumResultsForSearch: 20,
            templateResult: formatMemberDetails,
            templateSelection: formatMember
        };
    };

    function enableDisableButton(frm, status) {
        $(frm).find(":input[type=submit], :button[type=submit]").prop("disabled", status);
    }
    function timerIncrement() {
        idleTime = idleTime + 1;
        <?php if($_SESSION['curr_interface']=='staff'){ ?>
        lock_logout_session(idleTime);
        <?php } ?>
    }
    function lock_logout_session(localIdleTime) {
        // if (localIdleTime > 1) { // After 9 minutes, start polling the server for changes on the idle time from everyone,
        // whilst sending the current tab or window time
        $.ajax({
            url: "<?php echo site_url("welcome/clear_session_id"); ?>",
            type: "POST",
            data: {idleTime: localIdleTime},
            dataType: 'json',
            success: function (result) {
                idleTime = parseInt(result.idleTime);
                if (idleTime > 20 && idleTime < 40) { //when the overall idleTime is 20 mins, we should lock the screen
                    $('#myLockscreen').modal({backdrop: 'static', keyboard: false, show: true});
                } else {
                    if (idleTime === 40) { //log the user out after 40 minutes
                        window.location = "<?php echo site_url("welcome/logout/2"); ?>";
                    }
                    if ($('#myLockscreen').is(':visible')) {
                        $('#myLockscreen').modal('hide');
                    }
                }
            }
        });
        //}

    }
     function daterangepicker_initializer(ranges,min_date,max_date) {
     if(typeof drp!=='undefined'){
         drp.remove();
        }
        drp = $('#reportrange').data('daterangepicker');
    //Date range picker
    var cb = function (start, end, label) {
        $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
    };
    optionSet1 = {
        startDate: moment(start_date, 'X'),
        endDate: moment(end_date, 'X'),
        //minDate: '<?php echo date('d-m-Y'); ?>',
        //maxDate: '<?php echo date('d-m-Y'); ?>',
        /*dateLimit: {
         years: 3
         },*/
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        opens: 'left',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
        format: 'DD-MM-YYYY',
        separator: ' to ',
        locale: {
            applyLabel: 'Submit',
            cancelLabel: 'Clear',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        },
        ranges: {
            'Today': [moment(), moment()],
            //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(30, 'days'), moment()],
            'Last 60 Days': [moment().subtract(60, 'days'), moment()],
            'Last 90 Days': [moment().subtract(90, 'days'), moment()]
            //,'This Month': [moment().startOf('month'), moment().endOf('month')],
            //'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            ////'Full Records': [moment().subtract(1.5, 'year').startOf('month'), moment()]
        }
    };
    if (typeof ranges !== 'undefined' && ranges !== false){
        optionSet1['ranges'] = ranges;
    }
    if (( typeof min_date !== 'undefined' && min_date!==false)){
        optionSet1['minDate']= min_date;
    }
    if (( typeof max_date !== 'undefined' && max_date!==false )){
        optionSet1['maxDate']= max_date;
    }
        $('#reportrange span').html(moment(start_date, 'X').format('MMMM D, YYYY') + ' - ' + moment(end_date, 'X').format('MMMM D, YYYY'));
        $('#reportrange').daterangepicker(optionSet1, cb);

        $('#reportrange').on('show.daterangepicker', function () {
            //console.log("show event fired");
        });
        $('#reportrange').on('hide.daterangepicker', function () {
            //console.log("hide event fired");
        });
        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            startDate = picker.startDate.format('X');
            endDate = picker.endDate.format('X');
            handleDateRangePicker(startDate, endDate);
        });
        $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
            //console.log("cancel event fired");
        });
    }
    
</script>   

<script type="text/javascript">

$('#top-search').keyup( function(){
	var search_term = $(this).val();
	if(search_term!=''){
	//send an ajax request.....
	get_member_data({'search_term':search_term});
	//hide

	$(document).on('click','#pname',function(){
		
		//$('#searchid').val($(this).text());
		$('#result').fadeOut("slow");
	 //--------end of search function--------------------
	 });
	
	//--------hide the div on body click envent--------------------
	$(document).on('click','*',function(){
			$('#result').fadeOut("slow");
		});
	}
});

const capitalize = (s) => {
  if (typeof s !== 'string') return ''
  return s.charAt(0).toUpperCase() + s.slice(1)
}

    function saveData3(form) {
        var $form = $(form);
        var formData = new FormData($form[0]);
        var id = $form.attr('id');
         $("body").addClass("loading"); 
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function () {
                enableDisableButton(form, true);
            },

            success: function (feedback) {
                if (feedback.success) {
                    if (isNaN(parseInt($form.attr('id')))) {
                        $form[0].reset();
                        $modal = $form.parents('div.modal');
                        if ($modal.length) {
                            $($modal[0]).modal('hide');
                        }
                    }
                    setTimeout(function () {
                        var formId = $form.attr('id');
                        var tblId = formId.replace("form", "tbl");
                        if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                            dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                        }
                        if (typeof reload_data === "function") {
                            reload_data(formId, feedback);
                        }
                    }, 1000);
                    toastr.success(feedback.message, "Success");
                    $("body").removeClass("loading"); 
                } else {
                    toastr.warning(feedback.message, "Failure!");
                    $("body").removeClass("loading"); 
                    reload_data($form.attr('id'), feedback);
                }
            }, error: function (jqXHR, textStatus, errorThrown) {
                $("body").removeClass("loading"); 
                network_error(jqXHR, textStatus, errorThrown, form);
            },
            complete: function () {
                enableDisableButton(form, false);
            }
        });
    }

function get_member_data(new_data){
    var url = "<?php echo site_url("member/get_members_data"); ?>";
    $.ajax({
        url: url,
        data: new_data,
        type: 'POST',
        dataType:'json',
        success:function (response) {
            var clients= '';
            $.each(response.data, function(key,val){
                clients += "<a href='<?php echo site_url("member/member_personal_info"); ?>/"+val['id']+"'> <h4>"+capitalize(val['salutation'])+" "+capitalize(val['firstname'])+" "+capitalize(val['lastname'])+" "+capitalize(val['othernames'])+", "+val['client_no']
                +" - "+val['account_no']+" <span class='float-right'>"+val['date_registered']+"<span></h4> <ul class='list-unstyled m-t-md'> <li> <span class='fa fa-phone m-r-xs'></span> <label>Tel:</label>&nbsp;"+val['mobile_number']+"&nbsp;&nbsp;<span class='fa fa-envelope m-r-xs'></span> <label>Email:</label>"+val['email']+"</li></ul></a>";
              
            });
        if(clients ==''){
            clients= 'No search results found';
        }
        $('#result').fadeIn();
        $('#result').html('');
		$('#result').html(clients);
        },
        fail:function (jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
    });
}


$(document).on('keydown', function ( e ) {
// You may replace m with whatever key you want
    if ((e.metaKey || e.ctrlKey) && ( String.fromCharCode(e.which).toLowerCase() === 'k') ) {
        e.preventDefault();
        $("#modalSearch").modal('show');
    }
});

$(document).ready(() => {
// add select2 to security fees modal
document.querySelectorAll('.loan_security_fees')
        .forEach(select => {
            $(select).select2();
})
});


</script>
