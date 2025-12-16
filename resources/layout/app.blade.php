<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fault Sync</title>
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
    <script defer src="{{ asset('js/category.js') }}"></script>
</head>
<body>

<div class="wrapper">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <h2 class="logo">Fault Sync</h2>

        <div class="user">
            <div class="avatar"></div>
            <p>username</p>
            <span class="online">● online</span>
        </div>

        <ul class="menu">
            <li>Dashboard</li>
            <li>Bank Account</li>
            <li class="active">Category</li>
            <li>Transaction</li>
            <li>Summary</li>
        </ul>
    </aside>

    {{-- Main --}}
    <main class="main">
        <header class="topbar">
            <span>Tuesday, 12/12/2025</span>
            <span class="admin">Hey, Administrator</span>
        </header>

        <section class="content">
            @yield('content')
        </section>
    </main>
</div>

</body>
</html>