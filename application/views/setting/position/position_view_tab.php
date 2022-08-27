<div id="tab-position" class="tab-pane biodata" >
    <div class="panel-title pull-right">
    <?php if(in_array('1', $privileges)){ ?>
        <a href="#add_position-modal" data-toggle="modal"  class="btn btn-default btn-sm">
            <i class="fa fa-plus-circle"></i> New Position</a>
    <?php } ?>
    </div>
    <div class="table-responsive">
        <table class="table  table-bordered table-hover" id="tblPosition" width="100%" >
            <thead>
                <tr>
                    <th>Position Name</th>
                    <th>Description</th>
                    <th>Action</th>  
                </tr>
            </thead>
            <tbody>
         
            </tbody>
            
            </table>
			
        </div>
</div>
