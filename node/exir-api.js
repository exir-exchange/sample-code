const axios = require('axios');
const crypto = require('crypto');

// Replace these with your actual API key and secret
const API_KEY = '';
const API_SECRET = '';

// Base URL for EXIR API
const BASE_URL = 'https://api.exir.io';

// Function to generate the signature
function generateSignature(method, path, queryString, bodyString, expires) {
  const message = `${method}${path}${queryString}${expires}${bodyString}`;
  return crypto.createHmac('sha256', API_SECRET).update(message).digest('hex');
}

// Function to get the user's balance
async function getUserBalance() {
  const method = 'GET';
  const path = '/v2/user/balance';
  const expires = Math.floor(Date.now() / 1000) + 60; // Expires in 60 seconds
  const queryString = ''; // For GET request
  const bodyString = ''; // For GET request
  const signature = generateSignature(method, path, queryString, bodyString, expires);

  try {
    const response = await axios.get(`${BASE_URL}${path}`, {
      headers: {
        'api-key': API_KEY,
        'api-expires': expires,
        'api-signature': signature
      }
    });
    return response.data;
  } catch (error) {
    console.error('Error getting user balance:', error.response ? error.response.data : error.message);
  }
}

// Function to create an order
async function createOrder(symbol, side, size, price, type) {
  const method = 'POST';
  const path = '/v2/order';
  const expires = Math.floor(Date.now() / 1000) + 60; // Expires in 60 seconds
  const body = {
    symbol,
    side,
    size,
    type,
    price
  };
  const queryString = ''; // For POST request
  const bodyString = JSON.stringify(body);
  const signature = generateSignature(method, path, queryString, bodyString, expires);

  try {
    const response = await axios.post(`${BASE_URL}${path}`, body, {
      headers: {
        'api-key': API_KEY,
        'api-expires': expires,
        'api-signature': signature,
        'Content-Type': 'application/json'
      }
    });
    return response.data;
  } catch (error) {
    console.error('Error creating order:', error.response ? error.response.data : error.message);
  }
}

// Main function to execute the process
async function main() {
  const balance = await getUserBalance();
  console.log('User balance:', balance);

  // Replace these values with your desired order parameters
  const symbol = 'btc-usdt';
  const side = 'buy'; // or 'sell'
  const size = 0.01; // Order size in BTC
  const price = 30000; // Order price in USDT
  const type = 'limit';

  const order = await createOrder(symbol, side, size, price, type);
  console.log('Order response:', order);
}

main();