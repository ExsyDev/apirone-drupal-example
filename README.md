# Drupal Development Environment Setup with Docker

This guide will help you set up a local Drupal development environment using Docker.

---

## Prerequisites

Make sure you have the following installed on your system:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## Setup Instructions

### 1. Clone the Repository

Clone the project repository to your local machine:

```bash
git clone <repository_url>
cd <repository_directory>
```

### 2. Start the Containers

Use docker-compose to build and start the containers:

```bash
docker-compose up -d
```

Don't forger install composer dependencies

### 3. Access the Drupal Website

Once the containers are running, you can access the Drupal site at:

URL: http://localhost:8080
