                        <div role="tabpanel" id="tab-branch" class="tab-pane active"><br>
                            <div class="table-responsive">
                                <table id="tblOrganisation" class="table table-striped table-bordered table-hover small m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Initial</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive-->
                   
                            <div class="hr-line-dashed"></div>
                            <?php if(in_array('1', $privileges)){ ?>
                            <div><a data-toggle="modal" href="#add_branch-modal" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus-circle"></i> New Branch</a></div>
                            <?php } ?>
                            <h3><center>Branches and Departments</center></h3>
                            <div class="table-responsive">
                                <table id="tblBranch" class="table table-striped table-bordered table-hover small m-t-md" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Departments</th>
                                            <th>Telephone</th>
                                            <th>Email</th>
                                            <th>Physical Address</th>
                                            <th>Postal Address</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive-->
                        </div>

