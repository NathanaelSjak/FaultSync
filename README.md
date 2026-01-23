## Installation Guide
1. Install **Docker** on your host machine

    For **Windows Users**, follow the installation guide for Docker Desktop [here](https://docs.docker.com/desktop/setup/install/windows-install/).

    For **Mac Users**, follow the installation guide for Docker Desktop [here](https://docs.docker.com/desktop/setup/install/mac-install/)

    For **Linux Users**, follow the installation guide for Docker [here](https://docs.docker.com/engine/install/ubuntu/)

2. Once Docker is all set up (if you are using Docker Desktop, make sure it is running in the background), head to the root of this project, and run the following command,

    ```
    docker compose up --build -d
    ```

    This will migrate and run the Laravel, Nginx, and Database applications.

3. The web application will run on your local machine at **port 8000**

    ```
    localhost:8000
    ```

 > Note: Since this is a school project, any environment variable was shared, however, we have made sure to not publish any sensitive information

## Overview of the Website

1. **Login Page**
    
    The login page consists of two basic part, the form to provide your user credentials to access the application's main features, and a language switch between Bahasa Indonesia (ID) and English (EN); do note that all pages will have this language switch feature.
    
    This page is accessible as a landing page, as a redirect from the register page, and as the default redirect after logging out.

    ![Login Page](/images/desktop/login-page.png)

2. **Register Page**

    The register page requires the user's name, email, password, and confirm password as its input.

    Once the user is registered, they will be automatically logged in.

    This page is accessible from the login page.

    ![Register Page](/images/desktop/register-page.png)

3. **Side Bar**

    The side bar only shows up for authenticated users.

    The side bar consists of all menu accessible by a properly authenticated user.

    It also allows the user to log out using the button near their profile on the bottom.

    ![Side Bar](/images/desktop/side-bar.png)

4. **Header**

    This header only shows up for authenticated users.

    The header shows what page the user is currently in and the language switch.

    ![Header](/images/desktop/header.png)

5. **Dashboard**

    The dashboard shows a user's total income, total expenses, and net balance (total income - total expenses).

    The dashboard will also show the balance of each bank account the user has.

    Lastly, it will show the user's recent transaction histories.

    ![Dashboard](/images/desktop/dashboard.png)

    ![Dashboard Mobile](/images/mobile/image.png)

6. **Bank Account**

    When the user first enters the Bank Account menu, a list of the user's bank accounts will be shown.

    ![Bank Account List](/images/desktop/bank-account-list.png)
    
    ![Bank Account Mobile](/images/mobile/bank-account.png)

    The user could create a new bank account by clicking the top right create bank account button, it will then ask for the bank's name, the account's number, the type, and the optional description.
    
    ![Bank Account Create](/images/desktop/bank-account-create.png)

    The user could also edit a bank account by clicking the edit icon next to the bank account you want to edit, it will then show a similar form as the create one, but with pre-filled inputs

    ![alt text](/images/desktop/bank-account-edit.png)

    The user could also delete a bank account by clicking the delete icon next to the account they want to delete.

7. **Categories**

    When the user first enters the Categories menu, a list of the categories the user has made will be shown.

    ![Category List](/images/desktop/category-list.png)

    ![Category Mobile](/images/mobile/category.png)

    The user can filter their categories using either the search bar or the type (Income/Expense) dropdown, and then pressing apply.

    ![Filtered Category List](/images/desktop/filtered-category-list.png)

    The user can create a new category by clicking the create category button on the top right, it will then show a form that asks for the category name, type (income/expense), icon, color, and description.

    ![Category Create](/images/desktop/category-create.png)

    The user can edit a category by clicking the edit icon on the category card they want to edit, it will then show a form similar to the create one, but with pre-filled inputs.

    ![Category Edit](/images/desktop/category-edit.png)

    The user can also delete a category by clicking the delete icon on the category card they want to delete.

8. **Transactions**

    When the user first enters the Transactions menu, a list of the transactions the user has made will be shown.

    ![Transaction List](/images/desktop/transaction-list.png)

    ![Transaction Mobile](/images/mobile/transaction.png)

    The user can filter their transactions by account and/or category and/or date range, and then pressing apply. They can also press cancel to remove any filter.

    ![Filtered Transaction List](/images/desktop/filtered-transaction-list.png)

    The user can create a new transaction by clicking the create transaction button on the top right, it will then show a form that asks for the account, category, type (income/expense), amount, date, and description.

    ![Transaction Create](/images/desktop/transaction-create.png)

    The user can edit a transaction by clicking the edit icon on the transaction they want to edit, it will then show a form similar to the create one, but with pre-filled inputs.

    ![Transaction Edit](/images/desktop/transaction-edit.png)

    The user can also delete a transaction by clicking the delete icon on the transaction they want to delete.

9. **Profile**

    When the user enters the Profile menu, the user can update their profile, which includes their name, email, and password.

    To update their password, the user must confirm their new password as well.

    ![Profile](/images/desktop/profile.png)

    ![Profile Mobile](/images/mobile/profile.png)

    However, the user is also allowed to delete their own account, but this will only occur if the user confirms they want to delete their account by entering their current password to delete it.

    ![Profile Delete](/images/desktop/profile-delete.png)