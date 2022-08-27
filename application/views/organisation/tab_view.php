                        <div role="tabpanel" id="tab-organisation" class="tab-pane active">
                            <div class="hr-line-dashed"></div>
                            <?php if(in_array('1', $privileges)){ ?>
                            <div><a data-toggle="modal" href="#organisation-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New organisation</a></div>
                            <?php } ?>
                            <div class="table-responsive">
                            <table id="tblOrganisation" class="table table-striped table-hover small m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Initail</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                             </table>
                            </div><!-- /.table-responsive-->
                        </div>

