<?php
// Объявление PHP кода

// Определение пространства имен для модели
namespace app\models;

// Импорт базового класса Model
use yii\base\Model;
// Импорт класса для работы с провайдером данных
use yii\data\ActiveDataProvider;
// Импорт модели Book
use app\models\Book;

/**
 * Класс для поиска и фильтрации книг в библиотеке
 */
class BookSearch extends Model
{
    // Публичное свойство для поискового запроса
    public $search;
    // Публичное свойство для фильтрации по категории
    public $category_id;

    /**
     * Определение правил валидации для поисковых параметров
     */
    public function rules()
    {
        // Возвращает массив правил валидации
        return [
            // Поисковый запрос должен быть строкой
            [['search'], 'string'],
            // ID категории должен быть целым числом
            [['category_id'], 'integer'],
        ];
    }

    /**
     * Метод поиска книг с применением фильтров
     * @param array $params Параметры поиска из запроса
     */
    public function search($params)
    {
        // Создание базового запроса для поиска книг
        $query = Book::find();

        // Создание провайдера данных с пагинацией
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // Настройка пагинации - 12 книг на страницу
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        // Загрузка параметров поиска из запроса
        $this->load($params);

        // Проверка валидации параметров
        if (!$this->validate()) {
            // Возврат провайдера данных без фильтрации при ошибке валидации
            return $dataProvider;
        }

        // Применение фильтра по категории, если указан ID категории
        if (!empty($this->category_id)) {
            $query->andWhere(['category_id' => $this->category_id]);
        }

        // Применение поиска по названию и автору, если указан поисковый запрос
        if (!empty($this->search)) {
            $query->andWhere([
                'or', // Логическое ИЛИ для условий
                ['like', 'title', $this->search], // Поиск по названию
                ['like', 'author_firstname', $this->search], // Поиск по имени автора
                ['like', 'author_lastname', $this->search], // Поиск по фамилии автора
            ]);
        }

        // Возврат настроенного провайдера данных
        return $dataProvider;
    }
}
