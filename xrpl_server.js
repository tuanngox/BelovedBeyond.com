const express = require('express');
const xrpl = require('xrpl');

const app = express();
app.use(express.json());
const serverURL = 'wss://s1.ripple.com/';

/**
 * submit a payment
 */
app.post('/api/submit-payment', async (req, res) => {
  const { fromAddress, toAddress, amount, memo } = req.body;

  const client = new xrpl.Client(serverURL);
  await client.connect();

  const from_wallet = xrpl.Wallet.fromSeed(fromAddress);

  try {
    const payment = {
      TransactionType: "Payment",
      Account: from_wallet.classicAddress,
      Destination: toAddress,
      DestinationTag: memo, // Memo from Binance for correct deposit
      Fee: "12", // Set an appropriate fee
      Amount: xrpl.xrpToDrops(amount),
      ledger_index: current,
    //   LastLedgerSequence: (await client.getLedgerCount()) + 5, // Set the last ledger sequence
      Sequence: from_wallet.sequence
    };

    console.log(payment);

    const prepared = await client.autofill(payment);

    const signed = from_wallet.sign(prepared)

    // const signed = xrpl.sign(prepared, secret);
    const result = await client.submitAndWait(signed.tx_blob);

    console.log(result);

    res.json({
      success: true,
      transactionResult: result.result,
      hash: result.result.hash
    });
  } catch (error) {
    console.error('Error submitting payment:', error);
    res.status(500).json({ success: false, error: error.message });
  } finally {
    client.disconnect();
  }
});

/**
 * Create a new account
 */
app.post('/api/create-account', async (req, res) => {

    // const client = new xrpl.Client("wss://s.altnet.rippletest.net:51233")
    const client = new xrpl.Client(serverURL)

    await client.connect()

    try {

        const wallet = xrpl.Wallet.generate();

        console.log(wallet);

        res.json({
            success: true,
            address: wallet.classicAddress,
            secret: wallet.seed,
            balance:  0
        });

    } catch (error) {
        console.error('Error creating a new account:', error);
        res.status(500).json({ success: false, error: error.message });
      } finally {
        client.disconnect();
      }


  });

/**
 * get account info
 */
app.post('/api/get-account-info', async (req, res) => {
    const { address } = req.body;

    const client = new xrpl.Client(serverURL);
    await client.connect();

    try{
        // Fetch account info
        const accountInfo = await client.request({
            command: "account_info",
            account: address
            // ledger_index: 'validated'
            });

        res.json({
            success: true,
            result: accountInfo.result
            });

    } catch (error) {
        console.error('Error getting  account info:', error);
        res.status(500).json({ success: false, error: error.message });
    } finally {
    client.disconnect();
    }

  });

  /**
 * get transaction detail
 */
app.post('/api/transaction-detail', async (req, res) => {
    const { txHash } = req.body;

    const client = new xrpl.Client(serverURL);
    await client.connect();

    try{
        // Fetch account info
        const detail = await client.request({
            command: "tx",
            transaction: txHash
        });

        res.json({
            success: true,
            result: detail
            });

    } catch (error) {
        console.error('Error getting  account info:', error);
        res.status(500).json({ success: false, error: error.message });
    } finally {
    client.disconnect();
    }


  });


/**
 * get account info
 */
app.post('/api/get-transaction-history', async (req, res) => {
    const { address } = req.body;

    const client = new xrpl.Client(serverURL);
    await client.connect();

    try{

        // Fetch transaction history
        const response  = await client.request({
            command: "account_tx",
            account: address,
            ledger_index_min: -1,
            ledger_index_max: -1,
            limit: 30  // You can adjust this to retrieve more or fewer transactions
            });

        res.json({
            success: true,
            transactions: response.result.transactions
            });

    } catch (error) {
        console.error('Error getting transaction history:', error);
        res.status(500).json({ success: false, error: error.message });
    } finally {
    client.disconnect();
    }

  });


/**
 * connect wallet
 */
app.post('/api/connect-wallet', async (req, res) => {
    const { seed } = req.body;

    const client = new xrpl.Client(serverURL);
    await client.connect();

    try{
        const wallet = xrpl.Wallet.fromSeed(seed);  // User's secret (seed)
        console.log(`Wallet Address: ${wallet.address}`);

        res.json({
            success: true,
            wallet: wallet
            });

    } catch (error) {
        console.error('Error connecting to wallet:', error);
        res.status(500).json({ success: false, error: error.message });
    } finally {
    client.disconnect();
    }

  });


  /**
   * Get transaction fee
   */
  app.post('/api/get-transaction-fee', async (req, res) => {
    const { address } = req.body;

    const client = new xrpl.Client(serverURL);
    await client.connect();

    try{

        const fee = await client.request({ command: 'fee' });

        res.json({
            success: true,
            fee: fee['result']['drops']['open_ledger_fee'] /// drops : (1 XRP = 1,000,000 drops).  (1 drop = 0.000001 XRP)
            });

    } catch (error) {
        console.error('Error getting transaction fee:', error);
        res.status(500).json({ success: false, error: error.message });
    } finally {
    client.disconnect();
    }

  });


app.listen(5000, () => console.log('Server running on port 5000'));
