<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Layouts</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="index-2.html">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Layouts</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
             
            </div>
        </div>
    </div>
  
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-content text-center p-md">
					
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalMember">
						Add New Member
					</button>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalAddress">
						Add Address
					</button>
					
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalCountry">
						Add country
					</button>
			
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalDistrict">
						Add district
					</button>
					
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalSubcounty">
						Add Subcounty
					</button>
					
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalParish">
						Add Parish
					</button>
					
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModalVillage">
						Add Village
					</button>
			
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">

$(document).ready( function () {
$('#formMember').validator().on('submit', saveData);

$('#formaddress').validator().on('submit', saveData);

$('#formCountry').validator().on('submit', saveData);

$('#formDistrict').validator().on('submit', saveData);

$('#formCounty').validator().on('submit', saveData);

$('#formSubCounty').validator().on('submit', saveData);

$('#formParish').validator().on('submit', saveData);

$('#formVillage').validator().on('submit', saveData);

function reload_data(formId, reponse_data)
    {
		 window.location = "<?php  echo site_url('Member_add/'); ?>";
    }
});
</script>