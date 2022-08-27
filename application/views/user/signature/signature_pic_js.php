        var i_d = "<?php echo $user['user_id']; ?>";
             var $uploadCrop_sign,
                tempFilename,
                rawImg,
                imageId;
        function readFilee(input) {
            if (input.files && input.files[0]) {
                var signReader = new FileReader();
                signReader.onload = function (e) {
                    $('.upload-sh_sign').addClass('ready');
                    $('#signatureCropPop').modal('show');
                    rawImg = e.target.result;
                }
                signReader.readAsDataURL(input.files[0]);
            } else {
                alert("Sorry - you're browser doesn't support the FileReader API");
            }
        }

        $uploadCrop_sign = $('#upload-sh_sign').croppie({
            viewport: {
                width: 400,
                height: 200, type: 'square'
            },
            enforceBoundary: false,
            enableExif: true
        });
        $('#signatureCropPop').on('shown.bs.modal', function () {
                $uploadCrop_sign.croppie('bind', {
                url: rawImg
            });
        });

        $('.item-img_sign').on('change', function () {
            imageId = $(this).data('id');
            tempFilename = $(this).val();
            $('#cancelCropBtn').data('id', imageId);
             readFilee(this);
        });
        $('#cropImageBtn_sign').on('click', function (ev) {
            ;
            $uploadCrop_sign.croppie('result', {
                type: 'base64',
                format: 'jpeg',
                size: {width: 400, height: 200}
            }).then(function (resp) {
                $('#item-img-output_sign').attr('src', resp);
                $('#signatureCropPop').modal('hide');

                $.ajax({
            url: '<?php echo site_url("Signature/add_signature"); ?>',
            data: {image: resp, i_d: i_d,user_name:'<?php echo $user['firstname'].'_'.$user['lastname'];?>'},
            type: 'POST',
            dataType:'json',
            success:function (feedback) {
                     
                        $("#sign_pic_result").css({'background-image':'url('+feedback.response+')', 'background-repeat':'no-repeat','text-align':'center'}).removeClass('bg-placeholder').html('<br><br><br><center><h2 class="text-green">signature saved..</h2></center>');
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });

            });
        });
