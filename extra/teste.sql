USE `car_parking` ;

INSERT INTO tbl_user (name, email, password) VALUES ('Carlos', 'carlos@teste.com', '123456');
INSERT INTO tbl_user (name, email, password) VALUES ('Maria', 'maria@teste.com', '123456');

INSERT INTO tbl_owner (name, telephone) VALUES ('Bruno', '999999999');
INSERT INTO tbl_owner (name, telephone) VALUES ('Amanda', '999999998');

INSERT INTO tbl_car (brand_model, license_plate, tbl_owner_id) VALUES ('Fiat Uno', 'ABC-1234', 1);
INSERT INTO tbl_car (brand_model, license_plate, tbl_owner_id) VALUES ('Chevrolet Onix', 'ABC-1235', 2);
INSERT INTO tbl_car (brand_model, license_plate, tbl_owner_id) VALUES ('Toyota Corolla', 'ABC-1236', 1);

INSERT INTO tbl_register (date, entry_time, departure_time, tbl_user_id, tbl_car_id, tbl_car_tbl_owner_id) VALUES ('2020-01-01', '16:00:00', '18:00:00', 1, 1, 1);
INSERT INTO tbl_register (date, entry_time, departure_time, tbl_user_id, tbl_car_id, tbl_car_tbl_owner_id) VALUES ('2020-01-02', '11:00:00', '12:00:00', 2, 2, 2);
INSERT INTO tbl_register (date, entry_time, departure_time, tbl_user_id, tbl_car_id, tbl_car_tbl_owner_id) VALUES ('2020-01-03', '10:00:00', '12:00:00', 2, 3, 1);