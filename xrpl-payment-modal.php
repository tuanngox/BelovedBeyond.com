<div class="modal fade" id="paymentXrpl" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="/wallets/paymentXrpl" method="post">
        <div class="modal-content text-left">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?= __('Chuyển tiền'); ?></h4>
            </div>
            <div class="modal-body">


                <div class="">
                    <i class="fal fa-wallet"></i> <b><?= __("Bạn đang có: {0}", formatNumberCrypto($wallet['balance'] ?? 0)); ?></b>
                </div>
                <hr />

                <?php echo $this->Form->control("money", ['class' => "money-format", "max" => $wallet['balance'] ?? 0, 'label' => __('Nhập số tiền (XRP)'), 'value' => 0]); ?>
                <?php echo $this->Form->control("to_address", ['class' => "", 'label' => __('Ví nhận tiền'), 'placeholder' => __('Nhập địa chỉ ví nhận tiền')]); ?>
                <?php echo $this->Form->control("memo", ['class' => "", 'label' => __('Tag/Memo'), 'placeholder' => __('Nhập địa thông tin Tag / Memo (nếu có)')]); ?>

                <?php echo $this->Form->control("agree", ['class' => "", 'required' => true, 'type' => 'checkbox', 'hiddenField' => false, 'label' => __('Đồng ý điều khoản chuyển tiền của Beloved & Beyond')]); ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><i class="fal fa-check"></i> <?= __('Chuyển'); ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
            </div>
        </div>
        </form>
    </div>

</div>
