<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-5 col-lg-3">
    <!-- ========LOAD  MEMBER NAV BAR HERE =============== -->
    <?php echo $user_nav; ?>
    <!-- ========MEMBER NAV BAR =============== -->
    </div>
    <div class="col-xs-12 col-sm-6 col-md-7 col-lg-9" >
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                <div class="tab-content" style="min-height:500px;">
                    <!-- ================== START YOUR TAB CONTENT HERE =============== -->
                    <?php if ($type == "staff") { ?>
                        <?php $this->load->view('user/personalinfo/staff_data'); ?>
                        <?php $this->load->view('user/staff/role/role_view_tab'); ?>
                    <?php } ?>

                    <?php if ($type == "member") { ?>
                        <?php $this->load->view('user/personalinfo/personal_info'); ?>
                        <?php $this->load->view('user/member/children/children_tab'); ?>
                        <?php $this->load->view('user/member/business/business_view_tab'); ?>
                    <?php } ?>

                    <?php $this->load->view('user/contact/contact_view_tab'); ?>
                    <?php $this->load->view('user/address/address_view_tab'); ?>
                    <?php $this->load->view('user/nextofkin/nextofkin_view_tab'); ?>
                    <?php $this->load->view('user/employment/employment_view_tab'); ?>
                    <?php $this->load->view('user/password/password_view_tab'); ?>
                    <?php $this->load->view('user/document/document_view_tab'); ?>
                    <?php $this->load->view('user/signature/signature_view_tab'); ?>
                    <!-- ================== END YOUR  TAB CONTENT HERE =============== -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
