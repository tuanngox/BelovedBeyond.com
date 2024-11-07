<div class="modal fade" id="connectXrplWallet" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="/wallets/connectXrplWallet" method="post">
        <div class="modal-content text-left">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?= __('Kết nối ví XRPL đã có'); ?></h4>
            </div>
            <div class="modal-body">

                <?php echo $this->Form->control("seed", ['label' => __('Khóa riêng ví XRPL'), 'placeholder' => __('Nhập vào khóa riêng ví XRPL của bạn')]); ?>

                <?php echo $this->Form->control("agree", ['class' => "", 'type' => 'checkbox', 'hiddenField' => false, 'required' => true, 'label' => __('Đồng ý điều khoản chuyển tiền của Beloved & Beyond')]); ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fal fa-check"></i> <?= __('Kết nối ví'); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
            </div>
        </div>
        </form>
    </div>

</div>
