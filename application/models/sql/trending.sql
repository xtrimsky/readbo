SELECT i.id, l3.item_sid, l3.value, i.feed_id, i.title, i.content, i.date, i.link, i.author, i.picture, i.read_count
FROM (
    SELECT
        item_sid,
        (likes * 0.90 + viewed_posts * 0.10) AS value
    FROM(
        SELECT
            SUM(likes) AS likes,
            SUM(viewed_posts) AS viewed_posts,
            item_sid
        FROM(
                SELECT
                    count(item_sid) AS likes,
                    '0' AS viewed_posts,
                    item_sid
                FROM likes 
                WHERE
                    timestamp > <?php echo $min_timestamp; ?>
                GROUP BY item_sid
            UNION
                SELECT
                    '0' AS likes,
                    count(item_sid) AS viewed_posts,
                    item_sid
                FROM viewed_posts 
                WHERE
                    timestamp > <?php echo $min_timestamp; ?>
                GROUP BY item_sid
        ) AS list
        GROUP BY item_sid
    ) AS list2
    ORDER BY value DESC
    LIMIT 20
) AS l3
LEFT JOIN items AS i
ON i.item_sid = l3.item_sid