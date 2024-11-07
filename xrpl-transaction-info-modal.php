<div class="modal fade" id="transactionInfoModal" role="dialog" aria-labelledby="transactionInfoModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content text-left">
            <div class="modal-header">
                <h4 class="modal-title" id="transactionInfoModal"><?= __('Transaction Details'); ?></h4>
            </div>
            <div class="modal-body">
                <div id="transaction-details">
                    <p><strong>Transaction Hash:</strong> <span id="hash"><?= $info['hash']; ?></span></p>
                    <p><strong>Ledger Index:</strong> <span id="ledger_index"><?= $info['ledger_index']; ?></span></p>
                    <p><strong>Account:</strong> <span id="account"><?= $info['tx_json']['Account']; ?></span></p>
                    <p><strong>Destination:</strong> <span id="destination"><?= $info['tx_json']['Destination']; ?></span></p>
                    <p><strong>Delivered Amount:</strong> <span id="delivered_amount"><?= formatNumberCrypto($info['meta']['delivered_amount'] ?? 0); ?></span></p>
                    <p><strong>Transaction Fee:</strong> <span id="fee"><?= formatNumberCrypto($info['tx_json']['Fee'] ?? 0); ?></span></p>
                    <p><strong>Status:</strong> <span id="transaction_result"><?= $info['meta']['TransactionResult']; ?></span></p>
                    <p><strong>Validation Status:</strong> <span id="validated"><?= $info['validated'] ? 'True' : 'False'; ?></span></p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Đóng'); ?></button>
            </div>
        </div>
        </form>
    </div>

</div>
