<div class="modal fade" id="paymentXrpl" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="/wallets/exchangeXrpl2BB" method="post">
        <div class="modal-content text-left">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?= __('Đổi điểm'); ?></h4>
            </div>
            <div class="modal-body">

                <p><?= __('Bạn đang đổi điểm'); ?></p>
                <div class="exchange-panel cover bg_x">
                    <label><?= __('Từ ví'); ?>:</label><br />
                    <div class="wallet-info pd5">
                        <i class="fal fa-wallet"></i> <b><?= __("Ví XRPL: {0}", formatNumberCrypto($wallet['balance'] ?? 0)); ?></b>
                    </div>

                    <div class="wallet-info pd5">
                        <label><?= __('Đến ví'); ?>:</label><br />
                        <i class="fal fa-wallet"></i> <b><?= __("Ví điểm BB: {0}", formatNumberCrypto($walletBB['wallet']['credit'] ?? 0, 'BB')); ?></b>
                    </div>

                    <p class="pdl5"><small><?= __('Tỉ lệ chuyển đổi: {0} XRP = {1}', 1, formatNumberCrypto($wallet['bb_rate'], 'BB')); ?></small></p>

                </div>

                <hr />

                <?php echo $this->Form->control("money", ['id' => "money_exchange", 'class' => "money-format", 'style' => 'font-size:19px; padding: 25px 20px', 'data-rate' => $wallet['bb_rate'], "min" => 1, "max" => $wallet['balance'] ?? 0, 'label' => __('Nhập số tiền (XRP)'), 'value' => 1]); ?>

                <hr />
                <label><?= __('Số điểm BB sẽ nhận:'); ?></label>
                <p id="point_exchange" style="font-weight: 600; font-size:25px;"><?= formatNumberCrypto(1 * $wallet['bb_rate'], ''); ?> BB</p>
                <hr />
                <?php echo $this->Form->control("agree", ['class' => "", 'required' => true, 'type' => 'checkbox', 'hiddenField' => false, 'label' => __('Đồng ý điều khoản chuyển tiền của Beloved & Beyond')]); ?>
            </div>
            <div class="modal-footer">
                <?php if(!empty($wallet['balance'])): ?>
                    <button type="submit" class="btn btn-primary"><i class="fal fa-check"></i> <?= __('Đổi điểm'); ?></button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
            </div>
        </div>
        </form>
    </div>

</div>
