<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="col-md-7">
                        <div class="pull-right">
                            <div class="btn-group">
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                                <a class="btn btn-default" href="<?php echo site_url("supplier/update/" . $supplier['id']); ?>" title="Edit supplier details">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                    <?php } ?>
                    <?php if(in_array('4', $accounts_privilege)){ ?>
                                <a href="<?php echo site_url("supplier/del_supplier/" . $supplier['id']); ?>" class="btn btn-danger" title='Delete supplier details'><i class="fa fa-trash"></i> Delete</a>
                    <?php } ?>
                            </div>
                        </div>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <tr><th>Primary Contact</th><td><a href="tel:<?php echo $supplier['phone1']; ?>" title="Call now"><?php echo $supplier['phone1']; ?></a></td></tr>
                            <tr><th>Secondary Contact</th><td><a href="tel:<?php echo $supplier['phone2']; ?>" title="Call now"><?php echo $supplier['phone2']; ?></a></td></tr>
                            <tr><th>Primary Email</th><td><a href="mailto:<?php echo $supplier['email_contact1']; ?>" title="Email now"><?php echo $supplier['email_contact1']; ?></a></td></tr>
                            <tr><th>Secondary Email</th><td><a href="mailto:<?php echo $supplier['email_contact2']; ?>" title="Email now"><?php echo $supplier['email_contact2']; ?></a></td></tr>
                            <tr><th>Postal Address</th><td><?php echo $supplier['postal_address']; ?></td></tr>
                            <tr><th>Physical Address</th><td><?php echo $supplier['physical_address']; ?></td></tr>
                            <tr><th>Supplies</th><td><?php echo isset($supplier['supply_count']) ? $supplier['supply_count'] : 0; ?></td></tr>
                            <tr><th>Supplies Amount</th><td><?php echo isset($supplier['supply_sum']) ? number_format($supplier['supply_sum'] ): 0; ?></td></tr>
                            <tr><th>Bills</th><td><?php echo isset($supplier['bills_count']) ? $supplier['bills_count'] : 0; ?></td></tr>
                            <tr><th>Bills Amount</th><td><?php echo isset($supplier['bills_sum']) ? number_format($supplier['bills_sum'] ): 0; ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.box -->
    </div><!-- /.col-lg-5 -->
</div>