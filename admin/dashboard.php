<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<?php 
    session_start();
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    if(!isset($_SESSION['adminID']) && empty($_SESSION['adminID'])){
        header("Location: index.php");
        exit;
    }
?>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../common/fcss/all.min.css">
    <link rel="stylesheet" href="../common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="../common/zahraastyle.css?v=1.1">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="common/aside.css">
</head>
<body>
    <header class="admin-header">
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="admin-branding">لوحة تحكم يا فاطمة الزهراء</div>
        <div class="admin-user">
            مرحبًا، <?= $_SESSION['adminName'] ?>
        </div>
    </header>
    <?php include 'common/aside.php'; ?>
    <main class="admin-main">
        <h1>مرحبًا بك في لوحة التحكم</h1>
        <p>اختر من القائمة الجانبية للوصول إلى الأقسام.</p>

        <div class="dashboard-cards">
            <div class="card">
                <div class="card-icon"><i class="fa fa-users"></i></div>
                <div class="card-info">
                    <h3>0</h3>
                    <p>عدد الزوار</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fa fa-trophy"></i></div>
                <div class="card-info">
                    <h3>0</h3>
                    <p>عدد المسابقات</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fa fa-percent"></i></div>
                <div class="card-info">
                    <h3>0%</h3>
                    <p>نسبة النجاح</p>
                </div>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fa fa-book"></i></div>
                <div class="card-info">
                    <h3>0</h3>
                    <p>عدد الأدعية</p>
                </div>
            </div>
        </div>
        <div class="statistic">
            <div class="chart-container">
                <h2>عدد الزوار خلال آخر 7 أيام</h2>
                <canvas id="visitorsChart"></canvas>
            </div>

            <!-- الرسم البياني لنسب النجاح -->
            <div class="chart-container">
                <h2>نسب النجاح</h2>
                <canvas id="successChart"></canvas>
            </div>
        </div>
        <div class="compherstion">
            <div class="competitions-header">
                <h2>آخر 3 مسابقات</h2>
                <a href="add_competition.php" class="btn-add">إضافة مسابقة</a>
            </div>

            <div class="latest-competitions">
                <table>
                    <thead>
                        <tr>
                            <th>اسم المسابقة</th>
                            <th>عدد المشاركين</th>
                            <th>عدد العلامة التامة</th>
                            <th>نسبة النجاح</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>مسابقة 1</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0%</td>
                        </tr>
                        <tr>
                            <td>مسابقة 2</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0%</td>
                        </tr>
                        <tr>
                            <td>مسابقة 3</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include '../common/jslinks.php'?>
    <script src="common/aside.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // بيانات افتراضية للزوار خلال 7 أيام
        const visitorsData = [12, 19, 7, 15, 22, 30, 18];
        const visitorsLabels = ["اليوم 1","اليوم 2","اليوم 3","اليوم 4","اليوم 5","اليوم 6","اليوم 7"];

        new Chart(document.getElementById('visitorsChart'), {
            type: 'line',
            data: {
                labels: visitorsLabels,
                datasets: [{
                    label: 'عدد الزوار',
                    data: visitorsData,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });

        // بيانات افتراضية لنسبة النجاح
        const successData = [70, 30]; // 70% نجاح، 30% رسوب
        const successLabels = ["نجاح", "رسوب"];

        new Chart(document.getElementById('successChart'), {
            type: 'doughnut',
            data: {
                labels: successLabels,
                datasets: [{
                    data: successData,
                    backgroundColor: ['#28a745','#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>

</body>
</html>