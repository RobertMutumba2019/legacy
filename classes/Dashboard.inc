<?php 

class Dashboard extends BeforeAndAfter{
	
	public function getLinks(){
		
		$links = array(
			array()
		);
		
		return $links;
	}
	
	public function index(){    

        if($this->rgf("sysuser", user_id(), "user_id", "user_role")== 57){
            //Feedback::redirect(return_url().'requisition/new-requisition');
        }elseif($this->rgf("sysuser", user_id(), "user_id", "user_role")== 1082){
            //Feedback::redirect(return_url().'pending-approvals/requisitions');
        }else{
            //Feedback::redirect(return_url().'requisition/new-requisition');
        }
       // Feedback::redirect(return_url().'requisition/new-requisition');
	 	
	?>
	
	<div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">
                <?php  if($this->rgf("sysuser", user_id(), "user_id", "user_role")== 57){ ?>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
							<i class="fa fa-fx fa-users"></i>
                        </div>
                        <?php 
                        $x = new Db();
                        $ro = $x->select("SELECT * FROM sysuser");
                        $total = $x->num_rows();
                        ?>
                        <div class="content">
                            <div class="text">REGISTERED USERS</div>
                            <div class="number count-to" data-from="0" data-to="<?php echo $total; ?>" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                        <div class="icon">
                            <i class="fa fa-fx fa-circle"></i>
                        </div>
                        <?php 
                        $x = new Db();
                        $ro = $x->select("SELECT * FROM sysuser WHERE user_online = 1");
                        $total = $x->num_rows();
                        ?>
                        <div class="content">
                            <div class="text">ONLINE USERS</div>
                            <div class="number count-to" data-from="0" data-to="<?php echo $total; ?>" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
            <!-- #END# Widgets -->
            <br/><br/>
            <?php
            $reports = new Reports();
            $reports->serialNumberFinderDashboard2();
  
            
            ?>
            
	<?php
	}

}