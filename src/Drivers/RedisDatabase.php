<?php 
namespace SamsmithKruz\Database\Drivers;

use Exception;
use Predis\Client;

class RedisDatabase {
    private $redis;

    /**
     * Constructor to initialize Redis connection using Predis.
     *
     * @param array $config Redis connection configuration
     * @throws Exception if connection to Redis server fails.
     */
    public function __construct($config = [])
    {
        try {
            $this->redis = new Client([
                'scheme' => $config['scheme'] ?? 'tcp',
                'host' => $config['host'] ?? '127.0.0.1',
                'port' => $config['port'] ?? 6379,
                'timeout' => $config['timeout'] ?? 0.0,
            ]);
        } catch (Exception $e) {
            throw new Exception("Failed to connect to Redis server: " . $e->getMessage());
        }
    }

    // 🌟 BASIC CRUD OPERATIONS
    public function insert($key, $value, $expiration = 0)
    {
        $this->redis->set($key, $value);
        if ($expiration > 0) {
            $this->redis->expire($key, $expiration);
        }
        return true;
    }

    public function select($key)
    {
        return $this->redis->get($key);
    }

    public function delete($key)
    {
        return $this->redis->del([$key]);
    }

    public function update($key, $value, $expiration = 0)
    {
        return $this->insert($key, $value, $expiration);
    }

    public function increment($key, $amount = 1)
    {
        return $this->redis->incrby($key, $amount);
    }

    public function decrement($key, $amount = 1)
    {
        return $this->redis->decrby($key, $amount);
    }

    public function select_all()
    {
        return $this->redis->keys('*');
    }

    public function flush()
    {
        return $this->redis->flushall();
    }

    // 🌟 HASH OPERATIONS
    public function hash_insert($hash, $field, $value)
    {
        return $this->redis->hset($hash, $field, $value);
    }

    public function hash_select($hash, $field)
    {
        return $this->redis->hget($hash, $field);
    }

    public function hash_delete($hash, $field)
    {
        return $this->redis->hdel($hash, [$field]);
    }

    public function hash_select_all($hash)
    {
        return $this->redis->hgetall($hash);
    }

    // 🌟 LIST OPERATIONS
    public function push($list, $value)
    {
        return $this->redis->lpush($list, [$value]);
    }

    public function pop($list)
    {
        return $this->redis->rpop($list);
    }

    public function list_range($list, $start, $stop)
    {
        return $this->redis->lrange($list, $start, $stop);
    }

    // 🌟 SET OPERATIONS
    public function set_add($set, $value)
    {
        return $this->redis->sadd($set, [$value]);
    }

    public function set_remove($set, $value)
    {
        return $this->redis->srem($set, [$value]);
    }

    public function set_members($set)
    {
        return $this->redis->smembers($set);
    }

    public function set_intersect($set1, $set2)
    {
        return $this->redis->sinter([$set1, $set2]);
    }

    // 🌟 SORTED SET (ZSET) OPERATIONS
    public function zadd($zset, $score, $member)
    {
        return $this->redis->zadd($zset, [$score => $member]);
    }

    public function zrem($zset, $member)
    {
        return $this->redis->zrem($zset, [$member]);
    }

    public function zrange($zset, $start, $stop, $withScores = false)
    {
        return $this->redis->zrange($zset, $start, $stop, ['WITHSCORES' => $withScores]);
    }

    public function zrevrange($zset, $start, $stop, $withScores = false)
    {
        return $this->redis->zrevrange($zset, $start, $stop, ['WITHSCORES' => $withScores]);
    }

    public function zscore($zset, $member)
    {
        return $this->redis->zscore($zset, $member);
    }

    public function zrank($zset, $member)
    {
        return $this->redis->zrank($zset, $member);
    }

    // 🌟 PUB/SUB
    public function publish($channel, $message)
    {
        return $this->redis->publish($channel, $message);
    }

    public function subscribe($channel, $callback)
    {
        $this->redis->subscribe([$channel], function ($message) use ($callback) {
            $callback($message);
        });
    }

    // 🌟 EXPIRATION HANDLING
    public function set_expiration($key, $seconds)
    {
        return $this->redis->expire($key, $seconds);
    }

    public function get_ttl($key)
    {
        return $this->redis->ttl($key);
    }
}
