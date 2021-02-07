# Laravel Snippets - Livewire Auth

Реализация системы авторизации с использованием компонентов **Laravel Livewire**.
Пользователи могут регистрироваться, авторизовываться, сбрасывать пароль. Доступ к учетной записи только после подтверждения почтового адреса.

### Установка

Для запуска приложения требуется **Docker** и **Docker Compose**.

Для инициализации приложения выполнить команду:
```
make init
```

### Управление

Запуск:
```
make up
```

Остановка приложения:

```
make down
```

### Интерфейсы

Приложение - http://localhost:8080

Почта (MailHog) - http://localhost:8025

### Тесты

```
make backend-test
```

### Цель проекта

Код написан в образовательных целях. 
