<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// Mảng chứa các khối HTML
$foxtool_aff_top = array(
    '<div class="ft-aff-top1" id="ft-aff-top1">
        <span class="ft-aff-top1-cl" onclick="ftnone(event, \'ft-aff-top1\')">&#215;</span>
        <div class="ft-aff-top1-box">
            <img src="' . FOXTOOL_URL . 'img/ov/vultr.png" />
        </div>
        <div class="ft-aff-top1-box2">' . __('The best VPS today', 'foxtool') . ' <a target="_blank" href="https://www.vultr.com/?ref=8457224">' . __('Register now', 'foxtool') . '</a></div>
        <div class="ft-aff-top1-box3">' . __('Charged hourly, deducted monthly. Top 3 VPS in the world', 'foxtool') . '</div>
    </div>',
    '<div class="ft-aff-top1" id="ft-aff-top1">
        <span class="ft-aff-top1-cl" onclick="ftnone(event, \'ft-aff-top1\')">&#215;</span>
        <div class="ft-aff-top1-box">
            <img style="width:70px;" src="' . FOXTOOL_URL . 'img/ov/aiktp.png" />
        </div>
        <div class="ft-aff-top1-box2">' . __('Powerful AI tools', 'foxtool') . ' <a target="_blank" href="https://aiktp.com/r/13802">' . __('Register now', 'foxtool') . '</a></div>
        <div class="ft-aff-top2-box3">' . __('Write AI content, powerful AI tools and write SEO standard articles', 'foxtool') . '</div>
    </div>'
);
$foxtool_random_index = array_rand($foxtool_aff_top);
echo $foxtool_aff_top[$foxtool_random_index];
?>
<script>
    jQuery(document).ready(function($) {
        setTimeout(function() {
            $('.ft-aff-top1').animate({
                opacity: 0,
				height: 'toggle'
            }, 1000, function() {
                $(this).hide();
            });
        }, 10000); 
    });
</script>
<style>
.ft-aff-top1{
	margin-top:20px;
	background: linear-gradient(85deg, #fff 0%, rgba(255, 255, 255, 0) 100%);
	padding:20px;
	border-radius:10px;
	font-size:15px;
	border-top: 1px solid #ccc;
	border-left: 1px solid #ccc;
	position: relative;
}
.ft-aff-top1-cl {
    width: 20px;
    height: 20px;
    display: flex;
    border: 1px solid #007bfc;
    color: #007bfc;
    border-radius: 100%;
    align-items: center;
    justify-content: center;
	top:-5px;
	left:-5px;
	background:#fff;
	position: absolute;
	cursor: pointer;
}
.ft-aff-top1-box{
	display: flex;
    align-items: center;
}
.ft-aff-top1-box img{
	width:100px;
}
.ft-aff-top1-box span{
	font-weight:bold;
	margin-left:20px;
	color:#0056b1;
}
.ft-aff-top1-box2{margin-top:10px;font-weight:bold;}
.ft-aff-top1-box3, .ft-aff-top2-box3, .ft-aff-top3-box3{
	margin-top:10px;
	display:block;
	padding: 7px;
	border-radius:5px;
}
.ft-aff-top1-box3{
    background: linear-gradient(85deg, #d2e8ff94 0%, rgba(255, 255, 255, 0) 100%);
}
.ft-aff-top2-box3{
    background: linear-gradient(85deg, #e62c3121 0%, rgba(255, 255, 255, 0) 100%);
}
</style>
