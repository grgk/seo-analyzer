# Описание проекта

1. Проект выполнен на платформе в виде библиотеки для платформы `Laravel`
2. Примеры использования проектов расположены в папке `examples`
3. Запускается анализ следующим образом 
    `$results = (new Analyzer())->analyzeUrl('https://www.msn.com/en-us', 'msn');`
То есть создается объект `Analyzer` и запускается метод `analyzeUrl`
Имеется другой метод запуска кода
``
    $page = new Page('http://www.msn.com/en-us');
    $analyzer = new Analyzer($page);
``
При этом сначала создается `Page` и передается в конструктор `Analyzer`

4. Тесты:
    1. Файлы в папке`/Metric/metricsTestData`  пока не не испольуются. Это тестовые данные для сравнения результатов.
    2. Файлы в папке `/tests/TestCase/Metric/Mock` - это реализации абстрактного класса 
    3. Остальные файлы - это тесты, которые берут конкретные классы из проекта, создают объекты, выполняют необходимые действия 
    и сравнивают результаты функциями тестовых утверждений
5. В папке `HttpClient` находятся сущности для получения контента по `http`-запросам. Имеется папка с исключениями,
в котором исключения связаны с данным модулем. Имеется класс `Client`, которые реализует действия по запросам к 
внешним данным. Он наследуется от интерфейса `ClientInterface`. В последующем все передачи сущности `Client` 
происходят в проекте по этому интерфейсу. Непосредственного упомнинания, что класс `Client` присутствует в проекте
имеется только при точке входа выполнения. Реализовано это для реализациии 2-го принципа `SOLID`, принципа открытости-закрытости.
6. Входной основной класс `Analizer` всегда использует `Page` для анализа внешних ресурсов по `Http`-запросу.
7. В проекте используется паттерн `Фабрика Метрик`  в классе  `MetricFactory`, которая создает метрики в зависимости от переданной
конфигурации. Метрик много.
8. В папке `src\Metric\File` реализованы метрики для проверки из файлов 
9. В папке `src\Metric\Page` реализованы метрики для проверки страниц 
10. Опишу по какому принципу проверяются метрики, какие алгоритмы:
К сожаленью, после анализа кода реализации всех метрик обнаружил, что заявленное в описании не реализовано полностью. 
``
https - checks for SSL encryption,
redirect - checks URL for redirects,
page_size - analyzes page size,
load_time - analyzes page load time,
url_size - analyzes URl size,
meta - analyzes page meta tags,
headers - analyzes page HTML headers,
keyword_density - analyzes keyword density in page content,
keyword_density_headers - analyzes keyword density in HTML headers on page,
alt_attributes - analyzes images alt attributes content,
keyword_url - analyzes URL for specified SEO keyword,
keyword_path - analyzes URL: path for specified keyword,
keyword_title - analyzes page title for specified keyword,
keyword_description - analyzes page meta description for keyword,
keyword_headers - analyzes HTML headers for keyword,
keyword_density_keyword - analyzes page content keyword density for specified keyword,
robots - analyzes "robots.txt" file
sitemap - analyzes "sitemap.xml" file
``
11. Данный проект необходимо доделывать. Он еще сырой для полноценного использования