<?php
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Connection;
use Models\Users as Users;

final class usersTest extends TestCase
{
    public function testCanBeCreatedFromValidDBConnection(): void
    {
        $test_database_connection = $this->createMock(Connection::class);
        $this->assertInstanceOf(
            Users::class,
            new Users($test_database_connection)
        );
    }

    public function testSQLQuery(): void
    {
        $test_database_connection = $this->createMock(Connection::class);
        $test_database_connection->method('fetchAssoc')->will($this->returnArgument(0));
        
        $users = new Users($test_database_connection);

        $this->assertEquals(
            $users->get_user("test", "testPassword"),
            'SELECT * FROM users WHERE username = ? and password = SHA2(?, 256)'
        );
    }

    public function testSQLArgs(): void
    {
        $test_database_connection = $this->createMock(Connection::class);
        $test_database_connection->method('fetchAssoc')->will($this->returnArgument(1));
        
        $users = new Users($test_database_connection);

        $this->assertEquals(
            $users->get_user("test", "testPassword"),
            Array("test", "testPassword")
        );
    }
}