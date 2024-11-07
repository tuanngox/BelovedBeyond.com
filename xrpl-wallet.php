<?php $allowXrpl = true; ?>
<?php if(!empty($allowXrpl)): ?>
<div class="share-code ">
    <div class="row">

        <div class="col-md-12 text-left">
            <div class="cover tips">
                <p>
                    <i class="fal fa-wallet" style="margin-bottom: 0;"></i> <span><b style="    font-size: 19px;"><?= __('Ví cá nhân'); ?></b>
                    <?php if(!empty($user['xrpl']['address'])): ?>
                        <a href="#" class="view-transaction-history"><small><?= __('Lịch sử giao dịch'); ?></small></a></span>
                    <?php endif; ?>
                </p>

                <?php if(empty($user['xrpl']['address'])): ?>
                    <div class="">
                        <a id="create_wallet" class="btn btn-primary" href="#">
                            <?= __('Khởi tạo ví'); ?>
                        </a>

                        <a id="add_wallet" class="btn btn-outline-primary" href="#">
                            <?= __('Kết nối ví'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <p><?= __('Số dư: <b>{0}</b>', formatNumberCrypto($xrplWallet['balance'] ?? 0)); ?> (<?= formatNumberCrypto($xrplWallet['value_usd'] ?? 0, 'USD'); ?>)</p>
                    <p><?= __('Địa chỉ ví:'); ?> <a href="#" data-toggle="modal" data-target="#walletAddress" class="btn-link"><?= __("Hiển thị"); ?></a></p>

                    <?php if(empty($xrplWallet['active'])): ?>
                        <a data-toggle="modal" data-target="#walletActive" href="#" class="maincolor inblock pd10 mbt10 border"><small> <?= __('Ví chưa kích hoạt. Vui lòng nạp 10 XRP vào ví này để kích hoạt.'); ?></small></a>
                    <?php endif; ?>

                    <div>
                        <a href="#" id="xrpl_payment" class="btn btn-outline-primary"><?= __('Chuyển tiền'); ?></a>
                        <a href="#" id="xrpl_exchange" class="btn btn-outline-primary"><?= __('Đổi điểm BB'); ?></a>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="walletAddress" tabindex="-1" aria-labelledby="walletAddress" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="walletAddress"><?= __('Địa chỉ ví'); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label><?= __('Địa chỉ ví XRP của bạn:'); ?></label>
                                <p style="font-size: 19px; color: #da2128; padding: 10px 0;"><?= $xrplWallet['account']; ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
                            </div>
                            </div>
                        </div>
                    </div>

                    <?php if(empty($xrplWallet['active'])): ?>
                        <div class="modal fade" id="walletActive" tabindex="-1" aria-labelledby="walletActive" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="walletActive"><?= __('Kích hoạt ví'); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><?= __('Để kích hoạt ví này, bạn thực hiện như sau:'); ?></p>
                                <ul>
                                    <li><?= __('<u>Bước 1:</u> Dùng Binance hoặc 1 Exchange App để mua 15 XRP'); ?></li>
                                    <li>
                                        <?= __('<u>Bước 2:</u>  Chuyển XRP đã mua chuyển vào ví này'); ?>
                                        <p style="color: #da2128; padding: 10px 0;"><?= $xrplWallet['account']; ?></p>
                                    </li>
                                    <li><?= __('<u>Bước 3:</u>  Chờ giao dịch hoàn tất, tải lại trang cá nhân để xem số dư ví'); ?></li>
                                </ul>
                                <br />
                                <br />
                                <p><?= __('Chúc bạn thành công!'); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>


                <?php endif; ?>

                </div>

            </div>


    </div>
</div>
<script>
    $(function(){
        const url_prefix  = '<?= URL_BASE; ?>';
        var $btn = $('#create_wallet');
        if($btn.size() > 0){
            $btn.click(function(e){
                e.preventDefault();

                if(!confirm(Lang.__('<?= __("Bạn có chắc chắn muốn tạo ví không?"); ?>'))){
                    return;
                }

                // tạo ví
                $.when(
                    mx.core.loader.post('/API/wallet/createWallet', {})
                ).then(function(json){
                    alert(json.msg);
                    if(json.code == 'ok'){
                        window.location.reload();
                        // $modal.modal("hide");
                    } else{
                        // alert(json.msg);
                    }
                });

            });

            var $btnConnect = $('#add_wallet');

            // kết nối ví đã có
            $btnConnect.click(function(e){
                e.preventDefault();

                $.when(mx.core.loader.loadAjax(  url_prefix + '/pages/load/xrpl-connect-wallet-modal', true, true, 'html')).then(function(html){
                    var $modal = $(html);
                    mx.core.body.append($modal);
                    $modal.modal();
                });

            });

        } else {
            var $address = $('#wallet_address');
            $('.show-address').click(function(e){
                e.preventDefault();
                $address.slideToggle();
            });

            $('.view-transaction-history').click(function(e){
                e.preventDefault();

                $.when(mx.core.loader.loadAjax(  url_prefix + '/pages/load/xrpl-transaction-history-modal', true, true, 'html')).then(function(html){
                    var $modal = $(html);
                    mx.core.body.append($modal);
                    $modal.modal();
                });
            });

            $('#xrpl_payment').click(function(e){
                e.preventDefault();

                $.when(mx.core.loader.loadAjax(  url_prefix + '/pages/load/xrpl-payment-modal', true, true, 'html')).then(function(html){
                    var $modal = $(html);
                    mx.core.body.append($modal);
                    $modal.modal();
                });
            });

            $('#xrpl_exchange').click(function(e){
                e.preventDefault();

                $.when(mx.core.loader.loadAjax(  url_prefix + '/pages/load/xrpl-exchange-modal', true, true, 'html')).then(function(html){
                    var $modal = $(html);

                    var $point = $modal.find('#point_exchange');

                    // xử lý các sự kiện
                    var $text = $modal.find('#money_exchange');
                    var rate = parseFloat($text.data("rate"));
                    var min = parseFloat($text.attr("min"));
                    var max = parseFloat($text.attr("max"));

                    $text.keyup(function(e){
                        var val = parseFloat($text.val());

                        if(val < min){
                            alert(Lang.__('<?= __("Xin nhập giá trị chuyển đổi tối thiểu từ:"); ?> ' + min));
                            $text.val(min);
                            val = min;
                        } else if(val > max){
                            alert(Lang.__('<?= __("Xin nhập giá trị chuyển đổi phải nhỏ hơn hoặc bằng:"); ?> ' + max));
                            $text.val(max);
                            val = max;
                        }

                        var v = val * rate;
                        $point.html( mx.core.number.formatNumber(v) + " BB" ) ;
                    });

                    mx.core.body.append($modal);
                    $modal.modal();
                });
            });
        }
    });
</script>

<?php endif; ?>