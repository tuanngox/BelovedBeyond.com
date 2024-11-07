<div class="modal fade" id="transactionHistory" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:800px">
        <div class="modal-content text-left">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><?= __('Lịch sử giao dịch'); ?></h4>
            </div>
            <div class="modal-body">

            <?php if(empty($transactions)): ?>
                <p><?= __('Không tìm thấy giao dịch nào...'); ?></p>
            <?php else: ?>


                <table class="table">
                    <thead>
                        <th><?= __('Tình trạng'); ?></th>
                        <th><?= __('Thời gian'); ?></th>
                        <th><?= __('Số tiền'); ?></th>
                        <th><?= __('Phí'); ?></th>
                        <th><?= __('Từ ví -> Đến ví'); ?></th>
                    </thead>
                    <tbody>
                    <?php foreach($transactions as $t): ?>
                    <tr data-hash="<?= $t['hash']; ?>" class="curpt">
                        <td>
                            <?= $t['tx_json']['status']; ?>
                        </td>
                        <td>
                            <?= $t['close_time_iso']; ?>
                        </td>
                        <td>
                            <b class="<?= $t['tx_json']['type_sign'] == "-" ? "maincolor": "subcolor"; ?>" ><?= $t['tx_json']['type_sign'] . formatNumberCrypto($t['tx_json']['DeliverMax'] / 1000000); ?></b>
                        </td>
                        <td>
                            <?php if($t['tx_json']['type_sign'] == "-"): ?>
                                <?= formatNumberCrypto($t['tx_json']['Fee'] / 1000000); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?= __('Từ ví'); ?>:</small><br />
                            <span class="maincolor"><?= $t['tx_json']['Account']; ?></span>
                            <br />
                            <small><?= __('Đến ví'); ?>:</small><br />
                            <span class="subcolor"><?= $t['tx_json']['Destination']; ?></span>
                        </td>
                    </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            const url_prefix  = '<?= URL_BASE; ?>';
            $('#transactionHistory tr[data-hash]').click(function(e){
                e.preventDefault();

                var $this = $(this);

                $.when(mx.core.loader.loadAjax(  url_prefix + '/pages/load/xrpl-transaction-info-modal?tx=' + $this.data('hash'), true, true, 'html')).then(function(html){
                    var $modal = $(html);
                    mx.core.body.append($modal);
                    $modal.modal("show");
                });
            });

        });
    </script>
</div>