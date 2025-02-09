# **FizzBuzz API test**

## **📌 Project Overview**
This project is a **FizzBuzz API** built with **Symfony 6.4** (most recent Long-Term Support Release) and **API Platform 4.0** (current version). It follows the **Domain-Driven Design (DDD)** principles to ensure a clean architecture and maintainability.

The (only) API endpoint receives a start and end numbers, and generates the **FizzBuzz sequence**, stores the result in a database, and returns the generated sequence.

The following documentation explains the reasoning behind the architectural decisions, setup instructions, and additional comments regarding the implementation.

---

## **🛠️ Installation & Setup**

### **1️⃣ System Requirements**
Ensure you have the following installed:
- **Docker & Docker Compose**

You can check it by running the following command on your terminal:

```sh
docker compose version
```
You should see something like:
```
Docker Compose version v2.32.4
```

### **2️⃣ Clone the Repository**
```bash
git clone https://github.com/MajesticGray/iseazy_fizzbuzz_test.git
cd iseazy_fizzbuzz_test
```

### **3️⃣ Configure Environment Variables**
  
This project is preconfigured to use **port 3000**.  
If you want to change it, modify the `API_EXPOSED_PORT` variable at the end of the [`.env`](.env#L36) file.

### **4️⃣ Start the containers (Using Docker)**
```bash
docker compose up -d
```
This will start two containers:
1. **iseazy_backend_mysql** running a **MariaDB 10.7** database server.
2. **iseazy_backend_php** running a custom image, the instructions defined on the included [Dockerfile](Dockerfile). The image built is based on [php:8.4-apache](https://hub.docker.com/layers/library/php/8.4-apache/images/sha256-f722d3f411b2951405044dfe1c6a7ffd2bbd8662f4b7cfd7ab162974767a38a4), and includes the required PHP extensions and Composer for installing dependencies.

> Note: it may take a while to build. Please, be patient.

Also, the following volume will be created:
1. **iseazy-fizzbuzz-test_iseazy_db**. This volume contains the development and the test databases, and is automatically named after <project_directory>_iseazy_db.

> Note: the test and development databases will be initialized automatically, following the instructions defined on [iseazy_test.sql](docker-files/iseazy_test.sql) and [iseazy.sql](docker-files/iseazy.sql) SQL files respectively, no need to run migrations.

### **5️⃣ Install Dependencies**
```sh
docker exec -it iseazy_backend_php composer install
```

This will install all the required dependencies.

You're done! Now, you can access the project homepage at [http://localhost:3000](http://localhost:3000) (or your custom port, if you changed it in the .env file)  

Here you will see the default Symfony welcome page, with the debug bar and some information. This screen has been left unmodified to check the sanity of the symfony installation, but it should be removed on production.

### **6️⃣ Try it**

#### Via API request
Try a post to the API endpoint. This will generate, and persist, a fizzbuzz sequence.
From your host machine, run the following:

```sh
curl --location 'http://localhost:3000/desafio/fizz/buzz' \
--header 'Content-Type: application/json' \
--data '{
    "start": 1,
    "end": 5
}'
```

You should see a response similar to the following:

```json
{
   "data":{
      "id":"\/.well-known\/genid\/316e083453088de21bf1",
      "type":"FizzBuzzOutputDto",
      "attributes":{
         "start": 1,
         "end": 5,
         "fizzBuzz": "1, 2, Fizz, 4, Buzz",
         "createdAt": "2025-05-05T05:55:55+00:00"
      }
   }
}
```

This response follows the [JSON:API](https://jsonapi.org/) standard.

You can check the corresponding database record too:

```bash
docker exec -it iseazy_backend_mysql mysql -u root -proot -D iseazy -e "SELECT * FROM fizzbuzz_run"
```

It will output something like the following:
```bash
+----+---------------------+----------------+--------------+---------------------+
| id | created_at          | initial_number | final_number | fizz_buzz           |
+----+---------------------+----------------+--------------+---------------------+
|  1 | 2025-05-05 05:55:55 |              1 |            5 | 1, 2, Fizz, 4, Buzz |
+----+---------------------+----------------+--------------+---------------------+
```

#### Via Command

There's a command line utility to try the sequence generator without persisting the results to the database: `app:fizzbuzz:generate`

Example:

```bash
docker exec -ti iseazy_backend_php bin/console app:fizzbuzz:generate 1 15 
```

It will dump the first 15 sequence items to the console.

See `app:fizzbuzz:generate --help` for more details.

### **7️⃣ Run Tests**

Some tests have been bundled to test the project's health.
You can run all **unit**, **integration**, and **functional tests** at once with the following command:

```bash
docker exec -it iseazy_backend_php bin/phpunit
```

Or you can target only one group, either by type, design layer or by scope. The following groups are available:

- by type:
    - functional: all functional tests
    - integration: all integration tests
    - unit: all unit tests
- by scope:
    - api: all API tests
    - persistence: all persistence tests
- by design layer
    - domain: all domain layer tests
    - application: all application layer tests
    - integration: all integration layer tests


To run the tests from a group, use the --group parameter.
Example:

```bash
docker exec -it iseazy_backend_php bin/phpunit --group domain
```

Also, an integration pipeline to work with GitHub Actions has been setup on [.github/workflows/tests.yaml](.github/workflows/tests.yaml) to be run on push/pull requests, on the `development` and `main` branches.

You will find the workflows on: https://github.com/MajesticGray/iseazy_fizzbuzz_test/actions

---

## **📂 Project Architecture and Design Decisions**
This project follows the **DDD (Domain-Driven Design) principles**, ensuring a clear separation of concerns.

### Key Architectural Decisions

1. **Separation of Layers**  

    The Separation of Layers in Domain-Driven Design (DDD) ensures clean architecture, maintainability, and scalability by organizing the code into distinct concerns:
   - `Domain/` → **Business logic** (e.g., [FizzBuzzGenerator](src/Domain/Service/FizzBuzzGenerator.php))
   - `Application/` → **DTOs & Commands**
   - `Infrastructure/` → **Persistence, API Platform, Symfony integration**
   - `tests/` → **Unit, Integration, Functional tests**

    1.1. **Encapsulation** makes it sure that business logic remains independent of frameworks (Symfony and Doctrine).  
    1.2 **Scalability** makes it easier to modify persistence, APIs, or frameworks without affecting business logic.  
    1.3 **Unit tests** can focus on pure business logic without database concerns.  
    
    This flexibility allows us to switch databases, API formats, or frameworks with minimal changes, while ensuring a modular, maintainable, and future-proof application.

2. **Use of API Platform**  
   
   I chose API Platform for this project because it provides a robust, scalable, and efficient way to build APIs with Symfony, including:

   - **Automatic API Generation**  
      It reduces boilerplate code, making development faster and more maintainable.
      Built-in Validation & Serialization
   - **Decoupled & DDD-Friendly**  
      Supports DTOs and custom processors, allowing a clean separation of concerns.
      Fits well into the Domain-Driven Design (DDD) architecture.
   - **Extensibility and OpenAPI Support**  
      It can autogenerate OpenAPI documentation.
      Supports GraphQL, Hypermedia (JSON:API, HAL), and authentication.
   - **Performance Optimizations**  
      Supports caching, pagination, and filters out-of-the-box.
      Optimized database queries through Doctrine ORM integration.

   The API is defined using **DTOs**, **Processors**, and **Normalizers**.
   - [FizzBuzzRunProcessor](src/Infrastructure/ApiPlatform/Processor/FizzBuzzRunProcessor.php) handles the main API logic. It receives the input data from the API, calls the underlying [FizzBuzzGenerator](src/Domain/Service/FizzBuzzGenerator.php) service and manages the conversions from and to Domain models.
   - [FizzBuzzInputDto](src/Application/Dto/FizzBuzzInputDto.php) holds the input data for the API endpoint.
   - [FizzBuzzOutputDto](src/Application/Dto/FizzBuzzOutputDto.php) holds the output data to be sent, together with the response, to the client.
   - [FizzBuzzNormalizer](src/Infrastructure/ApiPlatform/Normalizer/FizzBuzzNormalizer.php) converts the invariable Domain [FizzBuzz](src/Domain/Model/FizzBuzz.php) model —with which the business logic operates— into the appropriate DTO for the final representation.

3. **Doctrine for Persistence**  

   Doctrine is ideal for complex applications where data consistency, abstraction, and maintainability are priorities.
   It grants us the following advantages:
   - Encapsulation & Abstraction
   - Easy integration with DDD patterns
   - Automatic Schema Management
   - Powerful and optimized queries without raw SQL.
   - Easier Maintainability and Database Agnostic, flexible for future changes.

   The entities ([FizzBuzzRun](src/Infrastructure/Doctrine/Entity/FizzBuzzRun.php)) are stored in a MariaDB database.
   The respository ([`FizzBuzzRunRepository`](src/Infrastructure/Persistence/Repository/FizzBuzzRunRepository.php)) allows querying the database encapsulating the code, and follows the Repository pattern.

4. **The FizzBuzz sequence generator service**

   Having a dedicated service ([FizzBuzzGenerator](src/Domain/Service/FizzBuzzGenerator.php)) in the Domain layer ensures separation of concerns and better code organization:

   1. Encapsulation of Business Logic
The FizzBuzz logic belongs to the domain, not controllers or repositories.  
   Keeps the business rules centralized and reusable.

   2. Improves Maintainability
If the FizzBuzz rules change, we modify only one class (FizzBuzzGenerator).

   3. Enhances Testability
The generator is pure business logic, making it easy to unit test independently.

   4. Why Use PHP Generators Instead of Arrays?
   Using PHP generators (yield) instead of arrays improves performance and memory efficiency:

        - PHP does not store the entire sequence in memory, reducing RAM usage for large ranges.
        - Improves performance, removing the need to precompute the entire sequence.
        - Suits better for streaming large datasets where we are returning paginated results.



5. **Testing Strategy**

    The tests are organized into folders following the same DDD priciples as the main code.

    - **Unit tests** for core business logic, including:
      - [FizzBuzzGeneratorTest](tests/Unit/Domain/FizzBuzzGeneratorTest.php)
         This file contains the following tests:  
         - **testFizzBuzzGenerationReturnsValidSequence**:  
         Ensures that the generation is working as expected, in a range from 30 to 67.

         - **testFizzBuzzGenerationThrowsOutOfRangeException**:  
         Ensures that a [FizzBuzzOutOfRangeException](src/Domain/Exception/FizzBuzzOutOfRangeException.php) is thrown when trying to generate more than `FIZZBUZZ_MAX_RANGE`. This variable is configurable in the .env files.

         - **testFizzBuzzGenerationEnsuresRangeCoherence**:  
         Ensures that a [FizzBuzzException](src/Domain/Exception/FizzBuzzException.php) is thrown when the end number is smaller than the start number.

         - **testFizzBuzzGenerationAcceptsNaturalNumbers**:  
         Ensures that a [FizzBuzzException](src/Domain/Exception/FizzBuzzException.php) is thrown when the start or end are not Natural numbers (> 0)

   - **Integration tests** for repository and services, including:
      - [FizzBuzzServiceTest](tests/Integration/Application/FizzBuzzServiceTest.php)
      This file contains the following tests:  

         - **testFizzBuzzGeneratorIsWiredCorrectly**:  
         Ensures the FizzBuzzGenerator service is injected correctly

         - **testFizzBuzzServiceReturnsSequenceGenerator**:  
         Ensures the service returns a sequence generator.
      - [FizzBuzzRunRepositoryTest](tests/Integration/Persistence/FizzBuzzRunRepositoryTest.php)
      This file contains the following tests:  

         - **testFizzBuzzRunIsPersistedCorrectly**:  
         Ensures that FizzBuzzRun entities are well persisted into the database

         - **testFizzBuzzRunCanBeDeleted**:  
         Ensures that FizzBuzzRun entities can be deleted.

   - **Functional tests** for API Platform behavior.
      - [FizzBuzzApiTest](tests/Functional/Api/FizzBuzzApiTest.php)
      This test tests the API endpoint end-to-end, including:

         - **testFizzBuzzApiReturnsCorrectResponse**:  
         Ensures that a valid API request returns a successful response and the expected data.  

         - **testFizzBuzzApiValidationFailure**:  
         Ensures that a bad API request returns the proper error and response code

         - **testFizzBuzzApiDatabasePersistence**:  
         Ensures that a valid API request ends up inserting the expected row into the database

---

## **📚 Main Libraries Used**
- **Symfony 6.4** → PHP Framework
- **API Platform 4.0** → API development
- **Doctrine ORM 3.3** → Database handling
- **PHPUnit 9.6** → Testing framework
- **Foundry 2.3** → Database ephemeral testing
- **Monolog 3.8** → Logging customization
- **Docker 27.4.1** → Local development
- **Docker compose v2.32.4** → Local development
---

## **💡 Additional Comments**

- **GitHub Actions CI/CD**
  - The project includes a **GitHub Actions pipeline** to automatically run tests on every push or pull request.

---

## **📩 Contact & Next Steps**
If you have any questions or want to discuss the project further, feel free to reach out on [hector.rovira@gmail.com](mailto:hector.rovira@gmail.com)

🚀 **Enjoy the API!**
