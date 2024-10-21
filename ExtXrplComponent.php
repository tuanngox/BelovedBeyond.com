<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Core\Configure;


class ExtXrplComponent extends AppComponent {

    private $apiServerURL = 'http://localhost:5000';
    private $bbWalletAddress = 'r3iSxVkSR7cUEDCh8Jm49dGQgcoN9wo4gE';

    public function initialize(array $config): void
    {
        if(SERVER_RUN_MODE == "REAL"){
            $this->apiServerURL = 'http://10.0.4.152:5000';
            $this->bbWalletAddress = 'rQptQ9Ju6JFUbUUpf1w9ZDWcrvZ3AqCizP';
        }

        parent::initialize($config);
    }

    /**
     * Lấy giá trị hiện tại
     */
    function getValueRealtime() : float {

        $json = $this->controller->Common->getLink('https://api.coingecko.com/api/v3/simple/price?ids=ripple&vs_currencies=usd', $type = 'json', $postData = null, $userpass = null, $headers = null);

        return  (float)$json['ripple']['usd'] ?? 0;
    }

    /**
     * Mã hóa
     */
    function _encrypter($string) : array {
        $key = Configure::read("Security.xrpl");

        $iv = openssl_random_pseudo_bytes(16);  // Initialization vector
        $encrypted_secret = openssl_encrypt($string, 'aes-256-cbc', $key, 0, $iv);


        return [$encrypted_secret, base64_encode($iv)];
    }

    /**
     * Giải mã
     */
    function _decrypter($encrypted_secret, $iv) : string {

        $iv = base64_decode($iv);

        $key = Configure::read("Security.xrpl");
        $decrypted_secret = openssl_decrypt($encrypted_secret, 'aes-256-cbc', $key, 0, $iv);

        return $decrypted_secret;
    }

    /**
     * Khởi tạo ví
     */
    function createWallet() : array {



        // kiểm tra xem user này đã có ví chưa?

        $userId = (string)$this->controller->Auth->user('_id');
        $user = $this->controller->_getRawInfo($userId, "users", ['xrpl']);

        if(!empty($user['xrpl']['address'])){
            return [
                'code' => 'ok',
                'msg' => __('Bạn đã có ví rồi!')
            ];
        }


        $ret = $this->_sendRequest('/api/create-account');


        if(!empty($ret['address'])){

            list($seed, $iv) = $this->_encrypter((string)$ret['secret']);

            $update = [
                '_id' => $userId,
                'xrpl' => [
                    'address' => (string)$ret['address'],
                    'seed' => $seed,
                    'iv' => $iv,
                    'balance' => (float)($ret['balance'] ?? 0) / 1000000,
                    'created' => time(),
                    'status' => 1,
                ]
            ];

            $this->controller->loadModel('User');
            if($this->controller->User->saveWithKeys($update)){
                return[
                    'code' => 'ok',
                    'msg' => __("Tạo ví thành công!")
                ];
            }

        }

        return[
            'code' => 'error',
            'msg' => __("Tạo ví thất bại! Xin vui lòng thử lại sau.")
        ];
    }

    /**
     * Lấy thông tin ví
     */
    function getWalletBalance() : array {
        $userId = $this->controller->Auth->user("_id");

        $user = $this->controller->_getRawInfo($userId, 'users', ['xrpl']);


        if(empty($user['xrpl'])){
            return [
                'code' => "error",
                "msg" => __('Bạn chưa có ví nào')
            ];
        }

        $wallet = $this->_sendRequest('/api/get-account-info', [
            "address" => $user['xrpl']['address']
        ]);

        // if(empty($wallet['result'])){
        //     return [
        //         "code" => "error",
        //         "msg" => __("Không tìm thấy thông tin ví XRPL của bạn")
        //     ];
        // }


        $xlp = 0;
        $active = false;

        // update
        if(isset($wallet['result']['account_data']['Balance'])){
            $updateUser = [
                '_id' => $userId,
                'xrpl.balance' => $xlp = ((float) ($wallet['result']['account_data']['Balance'] ?? 0) / 1000000),
            ];

            $active = true;
            $this->controller->loadModel('User');
            $this->controller->User->saveWithKeys($updateUser);
        } else {

        }


        $usd = $this->getValueRealtime();
        $usd2vndRate = 24500; // 1USD = 24500 VND
        return
        [
            'code' => "ok",
            "account" => $user['xrpl']['address'],
            "usd_rate" => $usd,
            'active' => $active,
            "usd_2vnd_rate" => $usd2vndRate,
            "balance" => $xlp,
            "value_usd" => $xlp * $usd
        ];
    }

    /**
     * Load lịch sử giao dịch
     */
    function getTransactionHistory() : array {
        $userId = $this->controller->Auth->user("_id");

        $user = $this->controller->_getRawInfo($userId, 'users', ['xrpl']);

        if(empty($user['xrpl'])){
            return [
                'code' => "error",
                "msg" => __('Bạn chưa có ví nào')
            ];
        }

        $transactions = $this->_sendRequest('/api/get-transaction-history', [
            "address" => $user['xrpl']['address']
        ]);

        return
        [
            'code' => "ok",
            "transactions" => $transactions['transactions'] ?? null
        ];
    }

    /**
     * Kết nối đến ví Xrpl có sẵn
     */
    function connectXrplWallet($data) : array {

        if(empty($data['seed'])){
            return [
                'code' => 'error',
                'msg' => __('Dữ liệu không hợp lệ. Xin vui lòng kiểm tra lại.')
            ];
        }

        // kiểm tra xem user này đã có ví chưa?

        $userId = (string)$this->controller->Auth->user('_id');
        $user = $this->controller->_getRawInfo($userId, "users", ['xrpl']);

        if(!empty($user['xrpl']['address'])){
            return [
                'code' => 'ok',
                'msg' => __('Bạn đã có ví rồi!')
            ];
        }


        $ret = $this->_sendRequest('/api/connect-wallet', ['seed' => $data['seed']]);


        if(!empty($ret['wallet'])){
            $wallet = &$ret['wallet'];

            list($seed, $iv) = $this->_encrypter((string)$wallet['seed']);

            // lấy thông tin ví
            $info = $this->_sendRequest('/api/get-account-info', [
                "address" => (string)$wallet['classicAddress']
            ]);

            $update = [
                '_id' => $userId,
                'xrpl' => [
                    'address' => (string)$wallet['classicAddress'],
                    'seed' => $seed,
                    'iv' => $iv,
                    'balance' => (float)$info['result']['account_data']['Balance'] / 1000000,
                    'created' => time(),
                    'status' => 1,
                ]
            ];

            $this->controller->loadModel('User');
            if($this->controller->User->saveWithKeys($update)){
                return[
                    'code' => 'ok',
                    'msg' => __("Kết nối ví thành công!")
                ];
            }

        }

        return[
            'code' => 'error',
            'msg' => __("Kết nối ví thất bại! Xin vui lòng thử lại sau.")
        ];
    }

    /**
     * Thanh toán (chuyển tiền)
     */
    function paymentXrpl($data) : array {

        $userId = $this->controller->Auth->user("_id");

        $user = $this->controller->_getRawInfo($userId, 'users', ['xrpl']);

        if(empty($user['xrpl'])){
            return [
                'code' => "error",
                "msg" => __('Bạn chưa có ví nào')
            ];
        }

        $paymentInfo = [
            "fromAddress" => $this->_decrypter($user['xrpl']['seed'], $user['xrpl']['iv']),
            "toAddress" => $data['to_address'],
            "amount" => (string)$data['money']
        ];

        if(!empty($data['memo'])){
            $paymentInfo['memo'] = (int)$data['memo'];
        }

        $ret = $this->_sendRequest('/api/submit-payment', $paymentInfo);

        return
        [
            'code' => "ok",
            "transactionResult" => $ret['transactionResult'] ?? null
        ];
    }


    /**
     * Đổi điểm từ XRPL -> BB
     */
    function exchangeXrpl2BB($data) : array {

        // thanh toán đến ví XRPL của BB
        $data['to_address'] = $this->bbWalletAddress;

        $walletXrpl = $this->getWalletBalance();


        $walletXrpl['bb_rate'] = round( $walletXrpl['usd_rate'] * $walletXrpl['usd_2vnd_rate'], 0);


        $ret = $this->paymentXrpl($data);


        // $ret['transactionResult']['validated'] = 1; /// TODO

        // $this->controller->ExtWallet->addCreditLoyaltyFromBill('670356a9f8906c1d240b31ad'); /// TODO
        // exit;

        $vv = ['code' => "error", 'msg' => __('Đổi điểm thất bại. Xin thử lại sau!')];

        if(!empty($ret['transactionResult']['validated'])){
            // tạo 1 đơn hàng nhập điểm cho người dùng
            $this->controller->loadComponent('ExtWallet');

            $bbWallet = $this->controller->ExtWallet->getBBWalletInfo();


            $addFund = [
                'credit' => $credit  = ($walletXrpl['bb_rate'] * $data['money']),
                "wallet_id" => $bbWallet['wallet']['_id'],
                'paid' => true,
                'note' => __('Đổi {0} thành {1}', formatNumberCrypto($data['money'], 'XRP'), formatNumberCrypto($credit, 'BB')),
                'state' => 3, // chờ xử lý
                'status' => 4, // chờ admin xử lý
                'payment' => [
                    'method' => 30, // XRPL
                    'transaction_no' => $ret['transactionResult']['hash']
                ]
            ];

            $vv = $this->controller->ExtWallet->addFunds($addFund);

            // cộng điểm
            if(!empty($vv['_id'])){
                $this->controller->ExtWallet->addCreditLoyaltyFromBill($vv['_id']);
            }
        }

        return $vv;
    }

    /**
     * Lấy thông tin chi tiết của 1 giao dịch
     */
    function getTransactionDetail($hash) : array {

        // lấy thông tin ví
        $info = $this->_sendRequest('/api/transaction-detail', [
            "txHash" => (string)$hash
        ]);

        if(!empty($info['result']['result'])){
            $info['result']['result']['tx_json']['Fee'] = $info['result']['result']['tx_json']['Fee']  / 1000000;
            $info['result']['result']['meta']['delivered_amount'] = ($info['result']['result']['meta']['delivered_amount'] ?? 0)  / 1000000;
        }
        return ['code' => 'ok', 'info' => $info];
    }
    /**
     * Gửi yêu cầu.
     *
     * @param string $prompt Nội dung yêu cầu.
     * @return string Phản hồi từ Google GenAI.
     */
    public function _sendRequest($path, $data = [])
    {

        $this->controller->loadComponent("File");
        list($response, $httpcode) = $this->controller->File->_sendPostRequest($this->apiServerURL . $path, $data);

        return $response;
    }
}
