import socket

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind(('127.0.0.1', 5555))
server_socket.listen(1)

print("Server listening on port 5555")

client_socket, client_address = server_socket.accept()
print(f"Connection from {client_address}")

data = client_socket.recv(1024).decode('utf-8')
print(f"Received message: {data}")

client_socket.close()
server_socket.close()
