# DESAFIO-BACKEND API

This api is a backend to execute bank transactions

## Table of Contents

- [Features](#features)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Configuration](#configuration)
- [Usage](#usage)
- [Docker](#docker)
- [Contributing](#contributing)
- [License](#license)

## Features

- **create transaction.** 

## Getting Started

### Prerequisites

- [PHP](https://www.php.net/downloads.php)
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Installation

1. Clone the project:
  ```bash
  git clone https://github.com/mendes-gmcb/desafio-backend.git
  ```
  ```bash
  cd desafio-backend
  ```

2. Copy env file:
  ```bash
  cp .env.example .env
  ```

3. Run the containers:
  ```bash
  docker-compose up --build
  ```

4. Access the container app 1 or 2:
  ```bash
  docker exec -it app1 bash
  ```

5. Install dependencies:
  ```bash
  composer install
  ```

6. Generate key for project:
  ```bash
  php artisan key:generate
  ```

7. Run the migrations:
  ```bash
  php artisan migrate
  ```

This will start the application, Redis server, and Nginx load balancer.

Acesse o projeto http://localhost:8080