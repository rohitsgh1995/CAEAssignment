# CAE Assignment

## Local Setup

1. **Setting Up Your Environment**
   - Open your `.env` file and add these lines:
     ```
     DB_CONNECTION=sqlite
     QUEUE_CONNECTION=database
     ```
   - Remove or comment out these lines if they exist:
     ```
     DB_HOST
     DB_PORT
     DB_DATABASE
     DB_USERNAME
     DB_PASSWORD
     ```

2. **Migrating the Database**
   - After updating the `.env` file, run:
     ```bash
     php artisan migrate
     ```
   - If asked, type 'yes' to create the database.

## Postman Setup

1. **Using Postman**
   - Import the provided Postman collection file located at:
      ```
      ./postman/CAEAssignment.postman_collection.json
      ```

## Usage

1. **Interacting with the API**
   - Explore the API endpoints for the CAE system.

2. **Start Queue before testing with Postman**
   - Run:
      ```bash
      php artisan queue:work
      ```

3. **Testing with Postman**
   - Use the imported Postman collection to test the API endpoints.

4. **Test Cases**
   - Run:
      ```bash
      php artisan test --testsuite=Feature --filter=APIEndPointsTest
      ```
   - It tests file upload, dispatched event, and all API endpoints.

## Conclusion

The CAE Assignment API is ready for testing. Follow the instructions to get started. Happy testing!