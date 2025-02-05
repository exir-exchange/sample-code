import requests
import hashlib
import hmac
import json
import time

# Replace these with your actual API key and secret
API_KEY = ''
API_SECRET = ''

# Base URL for EXIR API
BASE_URL = 'https://api.exir.io'

# Function to generate the signature
def generate_signature(method, path, query_string, body_string, expires):
    message = method + path + str(expires) + body_string
    print("message",message)
    secret = bytes(API_SECRET, 'utf-8')
    signature = hmac.new(secret, bytes(message, 'utf-8'), hashlib.sha256).hexdigest()
    return signature

# Function to get the user's balance
def get_user_balance():
    method = 'GET'
    path = '/v2/user/balance'
    expires = int(time.time()) + 60  # Expires in 60 seconds
    query_string = ''  # For GET request
    body_string = ''  # For GET request
    signature = generate_signature(method, path, query_string, body_string, expires)

    headers = {
        'api-key': API_KEY,
        'api-expires': str(expires),
        'api-signature': signature
    }

    try:
        response = requests.get(BASE_URL + path, headers=headers)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as error:
        print('Error getting user balance:', error)



# Function to create an order
def create_order(symbol, side, size, price, type):
    method = 'POST'
    path = '/v2/order'
    expires = int(time.time()) + 60  # Expires in 60 seconds
    body = {'symbol':symbol,'side':side,'size':size,'type':type,'price':price}
    query_string = ''
    body_string =  json.dumps(body).replace(" ","")
    print('body_string',body_string , len(body_string.replace(" ","")))
    signature = generate_signature(method, path, query_string, body_string, expires)
    headers = {
        'api-key': API_KEY,
        'api-expires': str(expires),
        'api-signature': signature,
        'Content-Type': 'application/json'
    }

    try:
        response = requests.post(BASE_URL + path , data=body_string, headers=headers)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as error:
        print('Error creating order:', error)
# Main function to execute the process
def main():
    balance = get_user_balance()
    print('User balance:', balance)

    # Replace these values with your desired order parameters
    symbol = 'btc-usdt'
    side = 'buy'  # or 'sell'
    size = 0.01  # Order size in BTC
    price = 30000  # Order price in USDT
    type = 'limit'

    order = create_order(symbol, side, size, price, type)
    print('Order response:', order)

if __name__ == "__main__":
    main()
