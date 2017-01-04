#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <libpq-fe.h>

static void exit_nicely(PGconn *conn)
{
    PQfinish(conn);
    exit(1);
}

void findResultOfQuery(PGresult *res)
{
    int rows = PQntuples(res);
    int columns = PQnfields(res);
    int i,j;
    for (i=0; i<columns; i++)
        printf("%-15s", PQfname(res, i));
    printf("\n");
    for(i=0; i<rows; i++)
    {
        for(j=0; j<columns; j++)
        {
            printf("%s ", PQgetvalue(res,i,j));
        }
        printf("\n");
    }
}

int main(int argc, char **argv)
{
    const char *conninfo;
    PGconn     *conn;
    PGresult   *res;
    const char *paramValues[1];
    int         paramLengths[1];
    int         paramFormats[1];
    unsigned long int    binaryIntVal;

    printf("\n\nTrying to establish a connection with server...\n\n");

    conn = PQconnectdb("hostaddr = '10.100.71.21' port = '5432' dbname = '201401058' user = '201401058' password = '1234567890' connect_timeout = '10'");

    if (PQstatus(conn) != CONNECTION_OK)
    {
        fprintf(stderr, "Connection to database failed: %s",
                PQerrorMessage(conn));
        exit_nicely(conn);
    }

    printf("\n\nConnection is established successfully with server!!!\n\n");

    char str[2048];
    while(1){
        printf("\n\nEnter Your Query Here:\n\n");
        char pathstr[] = "set search_path to \"railway_reservation_system\";" ;

        gets(str);
        char * str2 = "end";
        if(strcmp(str,str2) == 0)
            break;
        strcat(pathstr, str);
        res = PQexec(conn,pathstr);

        if (PQresultStatus(res) != PGRES_TUPLES_OK)
        {
            fprintf(stderr, "SELECT failed: %s", PQerrorMessage(conn));
            PQclear(res);
            exit_nicely(conn);
        }

        else
        {
            fprintf(stderr, "Command is working\n");
            findResultOfQuery(res);
        }

        PQclear(res);
    }
    PQfinish(conn);

    return 0;
}

